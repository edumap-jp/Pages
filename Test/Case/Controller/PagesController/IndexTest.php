<?php
/**
 * PagesController::index()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesController::index()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesController
 */
class PagesControllerIndexTest extends PagesControllerTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'pages';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'pages';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Pages', 'TestPages');
		Current::setSettingMode(false);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * index()アクションテスト
 *
 * @return void
 */
	public function testIndex() {
		//テスト実行
		$this->_testGetAction('/home', array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertEquals(1, preg_match_all('/' . preg_quote('test_pages/test_page/index', '/') . '/', $this->view));
	}

/**
 * 編集権限なしでセッティングONのindex()アクションテスト
 *
 * @return void
 */
	public function testSettingIndexWOLogin() {
		//テスト実行
		Current::setSettingMode(true);
		$this->_testGetAction('/setting/home', null, null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$this->assertTextContains('/home', $header['Location']);
	}

/**
 * index()アクションテスト
 *
 * @return void
 */
	//public function testIndexOnNotFound() {
	//	//テスト実行
	//	$this->_mockForReturnFalse('Pages.Page', 'getPageWithFrame');
	//	$this->_testGetAction('/aaaa', null, 'NotFoundException', 'view');
	//}

}
