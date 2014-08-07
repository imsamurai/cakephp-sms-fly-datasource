<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 18.11.2012
 * Time: 22:03:38
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('SMSFly', 'SMSFlySource.Model');
App::uses('SMSFlySource', 'SMSFlySource.Model/Datasource/Http');
App::uses('SMSFlyTestSource', 'SMSFlySource.Model/Datasource/Http');
App::uses('HttpSourceConnection', 'HttpSource.Model/Datasource');
App::uses('HttpSocketResponse', 'Network/Http');

class SMSFlySourceTest extends CakeTestCase {

	/**
	 * SMSFlySource Model
	 *
	 * @var AppModel
	 */
	public $Model = null;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->Model = new SMSFly(false, false, 'smsFlyTest');
	}

	/**
	 * Test get balance
	 * 
	 * @param array $request
	 * @param string $response
	 * @param float $result
	 * 
	 * @dataProvider getBalanceProvider
	 */
	public function testGetBalance(array $request, $response, $result) {
		$this->_mockConnection($request, $response);
		$this->assertSame($result, $this->Model->getBalance());
	}

	/**
	 * Data provider for testGetBalance
	 * 
	 * @return array
	 */
	public function getBalanceProvider() {
		return array(
			//set #0
			array(
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETBALANCE</operation></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'<balance>27.405</balance>' .
				"\n" .
				'</message>',
				//result
				27.41
			),
			//set #1
			array(
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETBALANCE</operation></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'</message>',
				//result
				false
			)
		);
	}

	/**
	 * Test get message status
	 * 
	 * @param string $id
	 * @param string $recipient
	 * @param array $request
	 * @param string $response
	 * @param float $result
	 * 
	 * @dataProvider getMessageStatusProvider
	 */
	public function testGetMessageStatus($id, $recipient, array $request, $response, $result) {
		$this->_mockConnection($request, $response);
		$this->assertSame($result, $this->Model->getMessageStatus($id, $recipient));
	}

	/**
	 * Data provider for testGetMessageStatus
	 * 
	 * @return array
	 */
	public function getMessageStatusProvider() {
		return array(
			//set #0
			array(
				//id
				'1',
				//recipient
				'80123456789',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETMESSAGESTATUS</operation><message campaignID="1" recipient="80123456789"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'<state campaignID="1" recipient="80123456789" status="DELIVERED" date="2014-08-06 17:07:00"></state>' .
				"\n" .
				'</message>',
				//result
				array(
					'id' => '1',
					'recipient' => '80123456789',
					'status' => 'DELIVERED',
					'date' => '2014-08-06 17:07:00'
				)
			),
			//set #1
			array(
				//id
				'1',
				//recipient
				'80987654321',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETMESSAGESTATUS</operation><message campaignID="1" recipient="80987654321"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'<state code="MSGERROR" date="2014-08-07 16:05:52">Message not found</state>' .
				"\n" .
				'</message>',
				//result
				false
			)
		);
	}

	/**
	 * Test get message details
	 * 
	 * @param string $id
	 * @param array $request
	 * @param string $response
	 * @param float $result
	 * 
	 * @dataProvider getDetailsProvider
	 */
	public function testGetDetails($id, array $request, $response, $result) {
		$this->_mockConnection($request, $response);
		$this->assertSame($result, $this->Model->getDetails($id));
	}

	/**
	 * Data provider for testGetDetails
	 * 
	 * @return array
	 */
	public function getDetailsProvider() {
		return array(
			//set #0
			array(
				//id
				'1',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETCAMPAIGNDETAIL</operation><message campaignID="1"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<answer>' .
				"\n" .
				'<campaign campaignID="1" createDateTime="2014-08-06 17:06:50" startDateTime="2014-08-06 17:06:50"  status="COMPLETE">' .
				"\n" .
				'<message phone="380123456789" part="1" parts="1" status="DELIVERED" startDateTime="2014-08-06 17:06:50" modifyDateTime="2014-08-06 17:07:00"></message>' .
				"\n" .
				'</campaign>' .
				"\n" .
				'</answer>',
				//result
				array(
					'id' => '1',
					'created' => '2014-08-06 17:06:50',
					'started' => '2014-08-06 17:06:50',
					'status' => 'COMPLETE',
					'messages' => array(
						0 => array(
							'phone' => '380123456789',
							'part' => '1',
							'parts' => '1',
							'status' => 'DELIVERED',
							'started' => '2014-08-06 17:06:50',
							'modified' => '2014-08-06 17:07:00'
						)
					)
				)
			),
			//set #1
			array(
				//id
				'2',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETCAMPAIGNDETAIL</operation><message campaignID="2"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<answer>' .
				"\n" .
				'<state code="CAMPAIGNERROR" date="2014-08-07 16:12:51">Incorrect campaign ID</state>' .
				"\n" .
				"\n" .
				'</answer>',
				//result
				false
			),
		);
	}

	/**
	 * Test get info
	 * 
	 * @param string $id
	 * @param array $request
	 * @param string $response
	 * @param float $result
	 * 
	 * @dataProvider getInfoProvider
	 */
	public function testGetInfo($id, array $request, $response, $result) {
		$this->_mockConnection($request, $response);
		$this->assertSame($result, $this->Model->getInfo($id));
	}

	/**
	 * Data provider for testGetDetails
	 * 
	 * @return array
	 */
	public function getInfoProvider() {
		return array(
			//set #0
			array(
				//id
				'1',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETCAMPAIGNINFO</operation><message campaignID="1"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<answer>' .
				"\n" .
				'<campaign campaignID="1" createDateTime="2014-08-06 17:06:50" startDateTime="2014-08-06 17:06:50"  status="COMPLETE">' .
				"\n" .
				'<state status="STOPFLAG" messages="0"></state>' .
				"\n" .
				'<state status="ALFANAMELIMITED" messages="0"></state>' .
				"\n" .
				'<state status="ERROR" messages="0"></state>' .
				"\n" .
				'<state status="USERSTOPED" messages="0"></state>' .
				"\n" .
				'<state status="STOPED" messages="0"></state>' .
				"\n" .
				'<state status="UNDELIV" messages="0"></state>' .
				"\n" .
				'<state status="EXPIRED" messages="0"></state>' .
				"\n" .
				'<state status="DELIVERED" messages="1"></state>' .
				"\n" .
				'<state status="SENT" messages="0"></state>' .
				"\n" .
				'<state status="PENDING" messages="0"></state>' .
				"\n" .
				'<state status="NEW" messages="0"></state>' .
				"\n" .
				'</campaign>' .
				"\n" .
				'</answer>',
				//result
				array(
					'id' => '1',
					'created' => '2014-08-06 17:06:50',
					'started' => '2014-08-06 17:06:50',
					'status' => 'COMPLETE',
					'state' => array(
						'STOPFLAG' => '0',
						'ALFANAMELIMITED' => '0',
						'ERROR' => '0',
						'USERSTOPED' => '0',
						'STOPED' => '0',
						'UNDELIV' => '0',
						'EXPIRED' => '0',
						'DELIVERED' => '1',
						'SENT' => '0',
						'PENDING' => '0',
						'NEW' => '0'
					)
				)
			),
			//set #1
			array(
				//id
				'2',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>GETCAMPAIGNINFO</operation><message campaignID="2"></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<answer>' .
				"\n" .
				'<state code="CAMPAIGNERROR" date="2014-08-07 16:20:53">Incorrect campaign ID</state>' .
				"\n" .
				'</answer>',
				//result
				false
			),
		);
	}

	/**
	 * Test send sms
	 * 
	 * @param string $source
	 * @param array $messages
	 * @param string $desc
	 * @param int $rate
	 * @param int $lifetime
	 * @param string $endTime
	 * @param string $startTime
	 * @param array $request
	 * @param string $response
	 * @param array $result
	 * 
	 * @dataProvider sendSMSProvider
	 */
	public function testSendSMS($source, array $messages, $desc, $rate, $lifetime, $endTime, $startTime, array $request, $response, $result) {
		$this->_mockConnection($request, $response);
		$this->assertSame($result, $this->Model->sendSMS($source, $messages, $desc, $rate, $lifetime, $endTime, $startTime));
	}

	/**
	 * Data provider for testSendSMS
	 * 
	 * @return array
	 */
	public function sendSMSProvider() {
		return array(
			//set #0
			array(
				//source
				'IOIX',
				//messages
				array(
					'HI Jack!' => '380987654321'
				),
				//desc
				'test',
				//rate
				'121',
				//lifetime
				'5',
				//endTime
				'AUTO',
				//startTime
				'AUTO',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>SENDSMS</operation><message source="IOIX" desc="test" rate="121" lifetime="5" end_time="AUTO" start_time="AUTO"><body>HI Jack!</body><recipient>380987654321</recipient></message></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'<state code="ACCEPT" campaignID="1" date="2014-08-07 16:30:12">The campaign has been successfully processed and added to the queue for delivery</state>' .
				"\n" .
				'<to recipient="380987654321" status="ACCEPTED" />' .
				"\n" .
				'</message>',
				//result
				array(
					'source' => 'IOIX',
					'messages' => array(
						'HI Jack!' => '380987654321'
					),
					'desc' => 'test',
					'rate' => '121',
					'lifetime' => '5',
					'endTime' => 'AUTO',
					'startTime' => 'AUTO',
					'date' => '2014-08-07 16:30:12',
					'to' => array(
						0 => array(
							'recipient' => '380987654321',
							'status' => 'ACCEPTED'
						)
					),
					'id' => '1'
				)
			),
			//set #1
			array(
				//source
				'IOIX',
				//messages
				array(
					'HI Jack!' => 'sdfsdfsdf'
				),
				//desc
				'test',
				//rate
				'121',
				//lifetime
				'5',
				//endTime
				'AUTO',
				//startTime
				'AUTO',
				//request
				array(
					'method' => 'POST',
					'body' => '<?xml version="1.0" encoding="utf-8"?><request><operation>SENDSMS</operation><message source="IOIX" desc="test" rate="121" lifetime="5" end_time="AUTO" start_time="AUTO"><body>HI Jack!</body><recipient>sdfsdfsdf</recipient></message></request>',
					'uri' => array(
						'host' => 'sms-fly.com',
						'port' => 80,
						'path' => '/api/api.php'
					)
				),
				//response
				'HTTP/1.1 200 OK' .
				"\r\n" .
				'Server: nginx/1.6.0' .
				"\r\n" .
				'Date: Thu, 07 Aug 2014 12:33:05 GMT' .
				"\r\n" .
				'Content-Type: text/html; charset=windows-1251' .
				"\r\n" .
				'Connection: close' .
				"\r\n" .
				'X-Powered-By: PHP/5.5.12' .
				"\r\n" .
				'Content-Length: 87' .
				"\r\n" . "\r\n" .
				'<?xml version="1.0" encoding="utf-8"?>' .
				"\n" .
				'<message>' .
				"\n" .
				'<state code="ERRPHONES" date="2014-08-07 16:31:44">No correct phones</state>' .
				"\n" .
				'</message>',
				//result
				false
			),
		);
	}

	/**
	 * Mock connection for test purposes
	 * 
	 * @param array $request
	 * @param string $response
	 */
	protected function _mockConnection($request, $response) {
		ConnectionManager::create('smsFlyTest', array(
			'datasource' => 'SMSFlySource.SMSFlyTestSource',
			'host' => 'sms-fly.com',
			'path' => '/api/api.php',
			'prefix' => '',
			'request' => array(
				'auth' => array(
					'method' => 'Basic',
					'user' => 'user',
					'pass' => 'pass'
				)
			),
			'port' => 80,
			'timeout' => 5
		));
		$this->Model = new SMSFly(false, false, 'smsFlyTest');
		$DS = $this->Model->getDataSource();
		$Connection = $this->getMock('SMSFlyTestHttpSourceConnection', array(
			'_request'
				), array($DS->config));
		$Connection->expects($this->once())->method('_request')->with($request)->will($this->returnValue(new HttpSocketResponse($response)));
		$DS->setConnection($Connection);
	}

}

/**
 * Source class for tests
 */
class SMSFlyTestSource extends SMSFlySource {

	/**
	 * {@inheritdoc}
	 * 
	 * @param array $config
	 * @param HttpSourceConnection $Connection
	 */
	public function __construct($config = array(), HttpSourceConnection $Connection = null) {
		parent::__construct($config, $Connection);
	}

	/**
	 * Method for inject mocked connection
	 * 
	 * @param HttpSourceConnection $Connection
	 */
	public function setConnection(HttpSourceConnection $Connection) {
		parent::__construct($this->config, $Connection);
	}

}

/**
 * Connection class for tests
 */
class SMSFlyTestHttpSourceConnection extends HttpSourceConnection {
	
}
