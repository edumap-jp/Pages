<?php
/**
 * SlugRoute::parse()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesModelTestCase', 'Pages.TestSuite');
App::uses('SlugRoute', 'Pages.Routing/Route');

/**
 * SlugRoute::parse()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Routing\Route\SlugRoute
 */
class PagesRoutingRouteSlugRouteParseTest extends PagesModelTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'pages';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Pages', 'TestPages');
	}

/**
 * DataProvider
 *
 * ### 戻り値
 * - template テンプレート
 * - url URL
 * - expected 期待値
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			// * 「/setting/」のケース #0
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/test4/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/aaaaa/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/test_pages/test_page/index',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/setting/',
				'expected' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index', 'pass' => array())
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/setting/test4/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/', 'url' => '/setting/aaaaa/',
				'expected' => false
			),
			// * 「/setting/*」のケース
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/test4/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/aaaaa/',
				'expected' => false
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/test_pages/test_page/index',
				'expected' => false
			),
			// #11
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/setting/',
				'expected' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index', 'pass' => array())
			),
			// #12 - テストエラー解消できず。ごめんギブアップ
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/setting/test4/',
				'expected' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index', 'pass' => array('test4'))
			),
			array('template' => '/' . Current::SETTING_MODE_WORD . '/*', 'url' => '/setting/aaaaa/',
				'expected' => false
			),
			// * 「/*」のケース
			array('template' => '/*', 'url' => '/',
				'expected' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index', 'pass' => array())
			),
			// #15 - テストエラー解消できず。ごめんギブアップ
			array('template' => '/*', 'url' => '/test4/',
				'expected' => array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index', 'pass' => array('test4'))
			),
			array('template' => '/*', 'url' => '/aaaaa/',
				'expected' => false
			),
			array('template' => '/*', 'url' => '/test_pages/test_page/index',
				'expected' => false
			),
			array('template' => '/*', 'url' => '/setting/',
				'expected' => false
			),
			array('template' => '/*', 'url' => '/setting/test4/',
				'expected' => false
			),
			array('template' => '/*', 'url' => '/setting/aaaaa/',
				'expected' => false
			),
		);
	}

/**
 * parse()のテスト
 *
 * @param string $template テンプレート
 * @param string $url URL
 * @param bool|array $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testParse($template, $url, $expected) {
		// 下記からコピー
		// NC3\app\vendors\cakephp\cakephp\lib\Cake\Test\Case\Routing\RouterTest.php::testSetRequestInfoLegacy()
		$request = array(
			array(
				'plugin' => 'pages', 'controller' => 'pages', 'action' => 'index',
				'url' => array('url' => 'pages/pages/index')
			),
			array(
				'base' => '',
				'here' => '/pages/pages/index',
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

		//テスト実施
		$route = new SlugRoute($template,
			array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index')
		);
		$route->compile();
		$result = $route->parse($url);

		//チェック
		if ($expected) {
			$this->assertEquals($expected['plugin'], $result['plugin']);
			$this->assertEquals($expected['controller'], $result['controller']);
			$this->assertEquals($expected['action'], $result['action']);
			$this->assertEquals($expected['pass'], $result['pass']);
		} else {
			$this->assertFalse($result);
		}
	}

}
