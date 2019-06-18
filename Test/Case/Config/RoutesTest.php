<?php
/**
 * Config/routes.phpのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsRoutesTestCase', 'NetCommons.TestSuite');

/**
 * Config/routes.phpのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Routing\Route\SlugRoute
 */
class RoutesTest extends NetCommonsRoutesTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'pages';

/**
 * DataProvider
 *
 * ### 戻り値
 * - url URL
 * - expected 期待値
 * - exception ExceptionError文字列(省略可)
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array(
				'url' => '/pages/page_edit/index',
				'expected' => array('plugin' => 'pages', 'controller' => 'page_edit', 'action' => 'index')
			),
		);
	}

/**
 * ルーティングのテスト
 *
 * @param string $url URL
 * @param bool|array $expected 期待値
 * @param bool $settingMode セッティングモード
 * @dataProvider dataProvider
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function testRoutes($url, $expected, $settingMode = false) {
		// 下記からコピー
		// NC3\app\vendors\cakephp\cakephp\lib\Cake\Test\Case\Routing\RouterTest.php::testSetRequestInfoLegacy()
		$request = array(
			array(
				'plugin' => 'pages', 'controller' => 'page_edit', 'action' => 'index',
				'url' => array('url' => $url)
			),
			array(
				'base' => '',
				'here' => '/' . $url,
				'webroot' => '/',
			)
		);
		// 下記からコピー
		// NC3\app\vendors\cakephp\cakephp\lib\Cake\Routing\Router.php::setRequestInfo()
		$requestObj = new CakeRequest($url);
		$request += array(array(), array());
		$request[0] += array('controller' => false, 'action' => false, 'plugin' => null);
		$requestObj->addParams($request[0])->addPaths($request[1]);
		Router::setRequestInfo($requestObj);

		parent::testRoutes($url, $expected, $settingMode);
	}
}
