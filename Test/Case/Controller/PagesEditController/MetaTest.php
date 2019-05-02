<?php
/**
 * PagesEditController::meta()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesEditController::meta()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\PagesEditController
 */
class PagesEditControllerMetaTest extends PagesControllerTestCase {

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
 * meta()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testMetaGet() {
		//テストデータ
		$roomId = '2';
		$pageId = '4';

		//テスト実行
		$this->_testGetAction(array('action' => 'meta', $roomId, $pageId), array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$this->assertInput('form', null, '/pages/pages_edit/meta/2/4', $this->view);
		$this->assertInput('input', '_method', 'PUT', $this->view);
		$this->assertInput('input', 'data[Page][id]', '4', $this->view);

		$this->assertInput('input', 'data[Page][id]', $pageId, $this->view);
		$this->assertInput('input', 'data[PagesLanguage][id]', '8', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][language_id]', '2', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][meta_title]', '{X-PAGE_NAME} - {X-SITE_NAME}', $this->view);
		$this->assertInput('input', 'data[PagesLanguage][meta_description]', null, $this->view);
		$this->assertInput('input', 'data[PagesLanguage][meta_keywords]', null, $this->view);
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
//
///**
// * meta()アクションのPOSTリクエストテスト
// *
// * @return void
// */
//	public function testPost() {
//		//テストデータ
//		$roomId = '2';
//		$pageId = '4';
//
//		$this->_mockForReturnTrue('Pages.PagesLanguage', 'savePagesLanguage');
//
//		$this->controller->Components->Flash
//			->expects($this->once())->method('set')
//			->with(__d('net_commons', 'Successfully saved.'));
//
//		//テスト実行
//		$this->_testPostAction('put', $this->__data(),
//				array('action' => 'meta', $roomId, $pageId), null, 'view');
//
//		//チェック
//		$header = $this->controller->response->header();
//		$this->assertTextContains('/pages/pages_edit/index/2/20', $header['Location']);
//	}
//
///**
// * meta()アクションのexistPage()のエラーテスト
// *
// * @return void
// */
//	public function testOnExceptionError() {
//		$roomId = '2';
//		$pageId = '4';
//		$this->_mockForReturnFalse('Pages.Page', 'existPage');
//
//		//テスト実行
//		$this->_testGetAction(array('action' => 'meta', $roomId, $pageId), null, 'BadRequestException', 'view');
//	}

/**
 * ValidationErrorテスト
 *
 * @return void
 */
	public function testOnValidationError() {
		$roomId = '2';
		$pageId = '4';
		$this->_mockForReturnCallback('Pages.PagesLanguage', 'savePagesLanguage', function () {
			$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Title tag'));
			$this->controller->PagesLanguage->invalidate('meta_title', $message);

			ClassRegistry::removeObject('PagesLanguage');
			ClassRegistry::addObject('PagesLanguage', $this->controller->PagesLanguage);
			return false;
		});

		//テスト実行
		$this->_testPostAction('put', $this->__data(),
				array('action' => 'meta', $roomId, $pageId), null, 'view');

		//チェック
		$message = sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Title tag'));
		$this->assertTextContains($message, $this->view);
	}

}
