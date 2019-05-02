<?php
/**
 * PagesEditController::edit()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesEditController::edit()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 */
class PagesEditControllerEditTest extends PagesControllerTestCase {

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
 * edit()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testEditGet() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';

		//テスト実行
		$this->_testGetAction(array('action' => 'edit', $roomId, $pageId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->__assertEditGet($roomId, $pageId);
	}

/**
 * edit()のチェック
 *
 * @param int $roomId ルームID
 * @param int $pageId ページID
 * @return void
 */
	private function __assertEditGet($roomId, $pageId) {
		$this->assertInput('form', null, '/pages/pages_edit/edit/' . $roomId . '/' . $pageId, $this->view);
		$this->assertInput('input', '_method', 'PUT', $this->view);
		$this->assertInput('input', 'data[Page][id]', $pageId, $this->view);
		$this->assertInput('input', 'data[Page][root_id]', '1', $this->view);
		$this->assertInput('input', 'data[Page][parent_id]', '1', $this->view);
		$this->assertInput('input', 'data[Page][room_id]', '2', $this->view);
		$this->assertInput('input', 'data[Room][id]', '2', $this->view);
		$this->assertInput('input', 'data[Room][space_id]', '2', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][id]', '8', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][language_id]', '2', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][name]', 'Home ja', $this->view);
		$this->assertInput('input', 'data[Page][permalink]', 'home', $this->view);
		$this->assertInput('input', 'data[_NetCommonsUrl][redirect]', null, $this->view);

		$this->controller->request->data = Hash::remove($this->controller->request->data, 'TrackableCreator');
		$this->controller->request->data = Hash::remove($this->controller->request->data, 'TrackableUpdater');

		$expected = array('PagesLanguage', 'Page', 'Language', 'Room', '_NetCommonsUrl');
		$this->assertEquals($expected, array_keys($this->controller->request->data));
		$this->assertEquals($pageId, Hash::get($this->controller->request->data, 'Page.id'));
		$this->assertEquals($roomId, Hash::get($this->controller->request->data, 'Page.room_id'));
		$this->assertEquals($roomId, Hash::get($this->controller->request->data, 'Room.id'));

		$this->assertInput('form', null, '/pages/pages_edit/delete/' . $roomId . '/' . $pageId, $this->view);
	}

/**
 * edit()アクションのSpaceのページアクセステスト
 *
 * @return void
 */
	public function testEditGetSpacePage() {
		$roomId = '2';
		$pageId = '1';

		//テスト実行
		$this->_testGetAction(array('action' => 'edit', $roomId, $pageId), null, 'BadRequestException', 'view');
	}

/**
 * edit()アクションのGETのExceptionErrorテスト
 *
 * @return void
 */
	public function testEditGetOnExceptionError() {
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturnFalse('Pages.Page', 'existPage');

		//テスト実行
		$this->_testGetAction(array('action' => 'edit', $roomId, $pageId), null, 'BadRequestException', 'view');
	}

/**
 * POSTリクエストデータ生成
 *
 * @return array リクエストデータ
 */
	private function __data() {
		$data = array(
			'_NetCommonsUrl' => array('redirect' => '/pages/pages_edit/index/2/20')
		);
		return $data;
	}

/**
 * edit()アクションのPOSTリクエストテスト
 *
 * @return void
 */
	public function testEditPost() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturn('Pages.Page', 'savePage', array(
			'Page' => array('id' => $pageId)
		));

		//テスト実行
		$this->_testPostAction('put', $this->__data(),
				array('action' => 'edit', $roomId, $pageId), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$this->assertTextContains('/pages/pages_edit/index/2/20', $header['Location']);
	}

/**
 * ValidationErrorテスト
 *
 * @return void
 */
	public function testEditPostValidationError() {
		$this->_mockForReturnCallback('Pages.Page', 'savePage', function () {
			$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name'));
			$this->controller->PagesLanguage->invalidate('name', $message);

			$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Slug'));
			$this->controller->Page->invalidate('permalink', $message);

			ClassRegistry::addObject('Page', $this->controller->Page);
			return false;
		});

		//テストデータ
		$roomId = '2';
		$pageId = '4';

		//テスト実行
		$this->_testPostAction('put', $this->__data(),
				array('action' => 'edit', $roomId, $pageId), null, 'view');

		//チェック
		$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name'));
		$this->assertTextContains($message, $this->view);

		$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Slug'));
		$this->assertTextContains($message, $this->view);
	}

}
