<?php
/**
 * PagesEditController::add()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesEditController::add()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 */
class PagesEditControllerAddTest extends PagesControllerTestCase {

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
	protected $_controller = 'pages_edit';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Pages', 'TestPages');
		//ログイン
		TestAuthGeneral::login($this);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * add()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testAddGet() {
		$roomId = '2';
		$pageId = '4';

		//テスト実行
		$this->_testGetAction(array('action' => 'add', $roomId, $pageId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertInput('form', null, '/pages/pages_edit/add/' . $roomId . '/' . $pageId, $this->view);
		$this->assertInput('input', '_method', 'POST', $this->view);
		$this->assertInput('input', 'data[Page][id]', null, $this->view);
		$this->assertInput('input', 'data[Page][root_id]', '1', $this->view);
		$this->assertInput('input', 'data[Page][parent_id]', $pageId, $this->view);
		$this->assertInput('input', 'data[Page][room_id]', '2', $this->view);
		$this->assertInput('input', 'data[Room][id]', '2', $this->view);
		$this->assertInput('input', 'data[Room][space_id]', '2', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][id]', null, $this->view);
		$this->assertInput('input', 'data[PagesLanguage][language_id]', '2', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][name]', null, $this->view);
		$this->assertInput('input', 'data[Page][permalink]', null, $this->view);
		$this->assertInput('input', 'data[_NetCommonsUrl][redirect]', null, $this->view);

		$this->assertEquals(array('Page', 'PagesLanguage', 'Room', '_NetCommonsUrl'), array_keys($this->controller->request->data));
		$this->assertEquals($pageId, Hash::get($this->controller->request->data, 'Page.parent_id'));
		$this->assertEquals($roomId, Hash::get($this->controller->request->data, 'Page.room_id'));
		$this->assertEquals($roomId, Hash::get($this->controller->request->data, 'Room.id'));
	}

/**
 * add()アクションのGETのExceptionErrorテスト
 *
 * @return void
 */
	public function testAddGetOnExceptionError() {
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturnFalse('Pages.Page', 'existPage');

		//テスト実行
		$this->_testGetAction(array('action' => 'add', $roomId, $pageId), null, 'BadRequestException', 'view');
	}

/**
 * POSTリクエストデータ生成
 *
 * @return array リクエストデータ
 */
	private function __data() {
		$data = array(
			'_NetCommonsUrl' => array('redirect' => '/pages/pages_edit/index/1/20')
		);
		return $data;
	}

/**
 * add()アクションのPOSTテスト
 *
 * @return void
 */
	public function testAddPost() {
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturn('Pages.Page', 'savePage', array(
			'Page' => array('id' => '20')
		));

		//テスト実行
		$this->_testPostAction('post', $this->__data(), array('action' => 'add', $roomId, $pageId), null);

		//チェック
		$header = $this->controller->response->header();
		$this->assertNotEmpty($header['Location']);
		$this->assertTextContains('/pages/pages_edit/index/1/20', $header['Location']);
	}

/**
 * add()アクションのPOSTのValidationErrorテスト
 *
 * @return void
 */
	public function testAddPostOnValidationError() {
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturnCallback('Pages.Page', 'savePage', function () {
			$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name'));
			$this->controller->PagesLanguage->invalidate('name', $message);

			$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Slug'));
			$this->controller->Page->invalidate('permalink', $message);

			ClassRegistry::addObject('Page', $this->controller->Page);
			return false;
		});

		//テスト実行
		$this->_testPostAction('post', array(), array('action' => 'add', $roomId, $pageId), null);

		//チェック
		$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name'));
		$this->assertTextContains($message, $this->view);

		$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Slug'));
		$this->assertTextContains($message, $this->view);
	}

}
