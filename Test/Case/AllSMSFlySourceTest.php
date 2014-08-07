<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Feb 7, 2014
 * Time: 4:17:11 PM
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * AllSMSFlySourceTest
 */
class AllSMSFlySourceTest extends PHPUnit_Framework_TestSuite {

	/**
	 * 	All SnatzAPISource tests suite
	 *
	 * @return PHPUnit_Framework_TestSuite the instance of PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All SMSFlySource Tests');
		$basePath = App::pluginPath('SMSFlySource') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($basePath);

		return $suite;
	}

}
