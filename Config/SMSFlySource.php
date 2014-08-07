<?php

/**
 * A SMSFlySource API Method Map
 *
 * Refer to the HttpSource plugin for how to build a method map
 *
 * @link https://github.com/imsamurai/cakephp-httpsource-datasource
 */
App::uses('HtmlHelper', 'View/Helper');
App::uses('View', 'View');
$Html = new HtmlHelper(new View);


$config['SMSFlySource']['config_version'] = 2;

$CF = HttpSourceConfigFactory::instance();
$Config = $CF->config();

$Config
		//Send sms
		->add(
				$CF->endpoint()
				->id(1)
				->methodCreate()
				->table('SENDSMS')
				->path('')
				->addCondition($CF->condition()->name('startTime')->map(null, 'start_time')->defaults('AUTO'))
				->addCondition($CF->condition()->name('endTime')->map(null, 'end_time')->defaults('AUTO'))
				->addCondition($CF->condition()->name('lifetime')->defaults(4))
				->addCondition($CF->condition()->name('rate')->defaults(120))
				->addCondition($CF->condition()->name('desc')->defaults(''))
				->addCondition($CF->condition()->name('source')->required())
				->addCondition($CF->condition()->name('messages')->required())
				->queryBuilder(function(Model $model, array $usedConditions, array $queryData) use ($Html) {
					$messagesData = $queryData['conditions']['messages'];
					array_walk($messagesData, function(&$phones) {
						$phones = is_array($phones) ? $phones : array($phones);
					});
					$messageOptions = array_intersect_key($queryData['conditions'], array_flip(array(
						'start_time', 'end_time', 'lifetime', 'rate', 'desc', 'source'
					)));
					$messages = '';
					if (count($messagesData) > 1) {
						$messageOptions['type'] = 'individual';
						foreach ($messagesData as $message => $phones) {
							foreach ($phones as $phone) {
								$messages .= $Html->tag('recipient', $phone);
								$messages .= $Html->tag('body', $message);
							}
						}
					} else {
						foreach ($messagesData as $message => $phones) {
							$messages .= $Html->tag('body', $message);
							$phones = is_array($phones) ? $phones : array($phones);
							foreach ($phones as $phone) {
								$messages .= $Html->tag('recipient', $phone);
							}
						}
					}

					$data = $Html->tag('operation', 'SENDSMS');
					$data .= $Html->tag('message', $messages, $messageOptions);

					$model->request['body'] = $data;
				})
				->result($CF->result()
						->map(function ($result, Model $Model) {
							if (!isset($result['message']['state']['@code'])) {
								$Model->data = false;
								$Model->getDataSource()->error = 'No state found';
							} elseif ($result['message']['state']['@code'] !== 'ACCEPT') {
								$Model->data = false;
								$Model->getDataSource()->error = $result['message']['state']['@code'];
							} else {
								$Model->id = $result['message']['state']['@campaignID'];

								$to = array();
								if (!isset($result['message']['to'][0])) {
									$result['message']['to'] = array($result['message']['to']);
								}
								foreach ($result['message']['to'] as $item) {
									$to[] = array(
										'recipient' => $item['@recipient'],
										'status' => $item['@status']
									);
								}
								$Model->set('date', $result['message']['state']['@date']);
								$Model->set('to', $to);

								return array(true);
							}
						})
				)
		)
		//Get balance
		->add(
				$CF->endpoint()
				->id(2)
				->methodRead()
				->table('GETBALANCE')
				->path('')
				->queryBuilder(function(Model $model, array $usedConditions, array $queryData) use ($Html) {
					$model->request['body'] = $Html->tag('operation', 'GETBALANCE');
				})
				->result($CF->result()
						->map(function ($result) {
							if (!isset($result['message']['balance'])) {
								return array();
							}
							return array(array(
									'balance' => $result['message']['balance']
							));
						})
				)
		)
		//Get info
		->add(
				$CF->endpoint()
				->id(3)
				->methodRead()
				->table('GETCAMPAIGNINFO')
				->path('')
				->addCondition($CF->condition()->name('id')->required())
				->queryBuilder(function(Model $model, array $usedConditions, array $queryData) use ($Html) {
					$data = $Html->tag('operation', 'GETCAMPAIGNINFO');
					$data .= $Html->tag('message', null, array('campaignID' => $queryData['conditions']['id']));
					$model->request['body'] = $data;
				})
				->result($CF->result()
						->map(function ($result, Model $Model) {
							if (empty($result['answer']['campaign'])) {
								$Model->getDataSource()->error = $result['answer']['state']['@'];
								return array();
							}
							$state = array();
							foreach ($result['answer']['campaign']['state'] as $stateSource) {
								$state[$stateSource['@status']] = $stateSource['@messages'];
							}
							return array(array(
									'id' => $result['answer']['campaign']['@campaignID'],
									'created' => $result['answer']['campaign']['@createDateTime'],
									'started' => $result['answer']['campaign']['@startDateTime'],
									'status' => $result['answer']['campaign']['@status'],
									'state' => $state
							));
						})
				)
		)
		//Get detail info
		->add(
				$CF->endpoint()
				->id(4)
				->methodRead()
				->table('GETCAMPAIGNDETAIL')
				->path('')
				->addCondition($CF->condition()->name('id')->required())
				->queryBuilder(function(Model $model, array $usedConditions, array $queryData) use ($Html) {
					$data = $Html->tag('operation', 'GETCAMPAIGNDETAIL');
					$data .= $Html->tag('message', null, array('campaignID' => $queryData['conditions']['id']));
					$model->request['body'] = $data;
				})
				->result($CF->result()
						->map(function ($result, Model $Model) {
							if (empty($result['answer']['campaign'])) {
								$Model->getDataSource()->error = $result['answer']['state']['@'];
								return array();
							}
							$messages = array();
							if (!isset($result['answer']['campaign']['message'][0])) {
								$result['answer']['campaign']['message'] = array($result['answer']['campaign']['message']);
							}
							foreach ($result['answer']['campaign']['message'] as $message) {
								$messages[] = array(
									'phone' => $message['@phone'],
									'part' => $message['@part'],
									'parts' => $message['@parts'],
									'status' => $message['@status'],
									'started' => $message['@startDateTime'],
									'modified' => $message['@modifyDateTime'],
								);
							}
							return array(array(
									'id' => $result['answer']['campaign']['@campaignID'],
									'created' => $result['answer']['campaign']['@createDateTime'],
									'started' => $result['answer']['campaign']['@startDateTime'],
									'status' => $result['answer']['campaign']['@status'],
									'messages' => $messages
							));
						})
				)
		)
		//Get message status
		->add(
				$CF->endpoint()
				->id(5)
				->methodRead()
				->table('GETMESSAGESTATUS')
				->path('')
				->addCondition($CF->condition()->name('id')->required())
				->addCondition($CF->condition()->name('recipient')->required())
				->queryBuilder(function(Model $model, array $usedConditions, array $queryData) use ($Html) {
					$data = $Html->tag('operation', 'GETMESSAGESTATUS');
					$data .= $Html->tag('message', null, array(
						'campaignID' => $queryData['conditions']['id'],
						'recipient' => $queryData['conditions']['recipient'],
					));
					$model->request['body'] = $data;
				})
				->result($CF->result()
						->map(function ($result, Model $Model) {
							if (!empty($result['message']['state']['@'])) {
								$Model->getDataSource()->error = $result['message']['state']['@'];
								return array();
							}
							$data = array(
								'id' => $result['message']['state']['@campaignID'],
								'recipient' => $result['message']['state']['@recipient'],
								'status' => $result['message']['state']['@status'],
								'date' => $result['message']['state']['@date'],
							);
							if (!empty($result['message']['state']['@'])) {
								$Model->getDataSource()->error = $result['message']['state']['@'];
							}
							return $data['id'] ? array($data) : array();
						})
				)
);
$config['SMSFlySource']['config'] = $Config;
