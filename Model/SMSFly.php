<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 07.08.2014
 * Time: 13:43:53
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * SMSFly Model
 * 
 * @package SMSFlySource
 * @subpackage Model
 */
class SMSFly extends AppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'SMSFly';

	/**
	 * {@inheritdoc}
	 *
	 * @var bool
	 */
	public $useTable = false;

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useDbConfig = 'smsFly';

	/**
	 * Send sms
	 * 
	 * @param string $source
	 * @param array $messages
	 * @param string $desc
	 * @param int $rate
	 * @param int $lifetime
	 * @param string $endTime
	 * @param string $startTime
	 * @return array|false
	 */
	public function sendSMS($source, array $messages, $desc = null, $rate = null, $lifetime = null, $endTime = null, $startTime = null) {
		$this->setSource('SENDSMS');
		$result = $this->save(array_filter(compact('source', 'messages', 'desc', 'rate', 'lifetime', 'endTime', 'startTime')));
		return $result ? $result[$this->alias] : $result;
	}

	/**
	 * Return current money balance
	 * 
	 * @return float|false
	 */
	public function getBalance() {
		$this->setSource('GETBALANCE');
		$result = $this->find('first');
		return $result ? (float)round($result[$this->alias]['balance'], 2) : false;
	}

	/**
	 * Get info
	 * 
	 * @param int $id
	 * @return array|false
	 */
	public function getInfo($id) {
		$this->setSource('GETCAMPAIGNINFO');
		$result = $this->find('first', array('conditions' => compact('id')));
		return $result ? $result[$this->alias] : false;
	}

	/**
	 * Get details
	 * 
	 * @param int $id
	 * @return array|false
	 */
	public function getDetails($id) {
		$this->setSource('GETCAMPAIGNDETAIL');
		$result = $this->find('first', array('conditions' => compact('id')));
		return $result ? $result[$this->alias] : false;
	}

	/**
	 * Get message status
	 * 
	 * @param int $id
	 * @param int|string $recipient
	 * @return array|false
	 */
	public function getMessageStatus($id, $recipient) {
		$this->setSource('GETMESSAGESTATUS');
		$result = $this->find('first', array('conditions' => compact('id', 'recipient')));
		return $result ? $result[$this->alias] : false;
	}

}
