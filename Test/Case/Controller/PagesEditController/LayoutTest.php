<?php
/**
 * PagesEditController::layout()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesEditController::layout()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 */
class PagesEditControllerLayoutTest extends PagesControllerTestCase {

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

		$this->generateNc(Inflector::camelize($this->_controller),
			['components' => [
				'Flash',
			]
		]);

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
 * layout()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testLayoutGet() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';

		//テスト実行
		$this->_testGetAction(array('action' => 'layout', $roomId, $pageId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertInput('form', null, '/pages/pages_edit/layout/2/4', $this->view);
		$this->assertInput('input', '_method', 'PUT', $this->view);
		$this->assertInput('input', 'data[Page][id]', '4', $this->view);

		$this->assertTextContains('0_0_0_0.png', $this->view);
		$this->assertTextContains('0_0_0_1.png', $this->view);
		$this->assertTextContains('0_0_1_0.png', $this->view);
		$this->assertTextContains('0_0_1_1.png', $this->view);
		$this->assertTextContains('0_1_0_0.png', $this->view);
		$this->assertTextContains('0_1_0_1.png', $this->view);
		$this->assertTextContains('0_1_1_0.png', $this->view);
		$this->assertTextContains('0_1_1_1.png', $this->view);
		$this->assertTextContains('1_0_0_0.png', $this->view);
		$this->assertTextContains('1_0_0_1.png', $this->view);
		$this->assertTextContains('1_0_1_0.png', $this->view);
		$this->assertTextContains('1_0_1_1.png', $this->view);
		$this->assertTextContains('1_1_0_0.png', $this->view);
		$this->assertTextContains('1_1_0_1.png', $this->view);
		$this->assertTextContains('1_1_1_0.png', $this->view);
		$this->assertTextContains('1_1_1_1.png', $this->view);
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
	public function testLayoutPost() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';

		$this->_mockForReturnTrue('Pages.PageContainer', 'savePageContainer');

		$this->controller->Components->Flash
			->expects($this->once())->method('set')
			->with(__d('net_commons', 'Successfully saved.'));

		//テスト実行
		$this->_testPostAction('put', $this->__data(),
				array('action' => 'layout', $roomId, $pageId), null, 'view');

		//チェック
		$header = $this->controller->response->header();
		$this->assertTextContains('/pages/pages_edit/index/2/20', $header['Location']);
	}

/**
 * edit()アクションのPOSTリクエストのExceptionErrorテスト
 *
 * @return void
 */
	public function testLayoutPostOnExceptionError() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';

		$this->_mockForReturnFalse('Pages.PageContainer', 'savePageContainer');

		//テスト実行
		$this->_testPostAction('put', $this->__data(),
				array('action' => 'layout', $roomId, $pageId), 'BadRequestException', 'view');
	}

}
