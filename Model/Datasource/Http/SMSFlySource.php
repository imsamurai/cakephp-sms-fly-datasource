<?php

/**
 * SMSFlySource DataSource
 *
 * DataSource for http://sms-fly.com/
 */
App::uses('HttpSource', 'HttpSource.Model/Datasource');
App::uses('HtmlHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * SMSFlySource
 * 
 * @package SMSFlySource
 * @subpackage Model.Datasource.Http
 */
class SMSFlySource extends HttpSource {

	/**
	 * Http methods constants
	 */
	const HTTP_METHOD_READ = 'POST';
	const HTTP_METHOD_CREATE = 'POST';
	const HTTP_METHOD_UPDATE = 'POST';
	const HTTP_METHOD_DELETE = 'POST';
	const HTTP_METHOD_CHECK = 'POST';

	/**
	 * The description of this data source
	 *
	 * @var string
	 */
	public $description = 'SMSFlySource DataSource';

	/**
	 * Constructor
	 * 
	 * @param array $config
	 * @param HttpSourceConnection $Connection
	 * @throws RuntimeException
	 */
	public function __construct($config = array(), HttpSourceConnection $Connection = null) {
		parent::__construct($config, $Connection);
		$this->setDecoder('text/html', function(HttpSocketResponse $HttpSocketResponse) {
			$Xml = Xml::build((string)$HttpSocketResponse);
			$response = Xml::toArray($Xml);
			return $response;
		}, true);
	}

	/**
	 * Sends HttpSocket requests. Builds your uri and formats the response too.
	 *
	 * @param Model $model Model object
	 * @param mixed $requestData Array of request or string uri
	 * @param string $requestMethod read, create, update, delete
	 *
	 * @return array|false $response
	 */
	public function request(Model $model = null, $requestData = null, $requestMethod = HttpSource::METHOD_READ) {
		if ($model === null) {
			return parent::request($model, $requestData, $requestMethod);
		}

		$HtmlHelper = new HtmlHelper(new View);
		unset($model->request['uri'], $model->request['virtual']);
		$model->request['body'] = '<?xml version="1.0" encoding="utf-8"?>' .
		$HtmlHelper->tag('request', $model->request['body']);

		return parent::request($model, $requestData, $requestMethod);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Model $model
	 * @param array $result
	 * @param string $requestMethod
	 * @param bool $force
	 * @return array
	 */
	protected function _extractResult(Model $model, array $result, $requestMethod, $force = true) {
		return parent::_extractResult($model, $result, $requestMethod, $force);
	}

}
