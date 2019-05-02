<?php
/**
 * PageLayoutComponent::beforeRender()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PageLayoutComponent::beforeRender()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\Component\PageLayoutComponent
 */
class PageLayoutComponentBeforeRenderTest extends PagesControllerTestCase {

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
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Pages', 'TestPages');

		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutComponent');

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
 * beforeRender()のテスト
 *
 * @return void
 */
	public function testBeforeRender() {
		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_component/index?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestPageLayoutComponent/index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertNotEmpty($this->vars['page']);
		$this->assertNull($this->vars['modal']);
		$this->assertArrayNotHasKey('NetCommons.Composer', $this->controller->helpers);
		$this->assertTrue(in_array('NetCommons.Composer', $this->controller->helpers, true));

		$this->assertArrayHasKey('Pages.PageLayout', $this->controller->helpers);
	}

/**
 * viewVars['page']がないテスト
 *
 * @return void
 */
	public function testBeforeRenderWOPage() {
		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_component/index_wo_page?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestPageLayoutComponent/index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertNotEmpty($this->vars['page']);
		$this->assertNull($this->vars['modal']);
		$this->assertArrayNotHasKey('NetCommons.Composer', $this->controller->helpers);
		$this->assertTrue(in_array('NetCommons.Composer', $this->controller->helpers, true));

		$this->assertArrayHasKey('Pages.PageLayout', $this->controller->helpers);
	}

/**
 * コントローラに'Pages.PageLayout'と'NetCommons.Composer'が定義されているテスト
 *
 * @return void
 */
	public function testBeforeRenderWithHelpers() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutComponentWHelpers');

		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_component_w_helpers/index?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestPageLayoutComponent/index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertNotEmpty($this->vars['page']);
		$this->assertNull($this->vars['modal']);
		$this->assertArrayHasKey('NetCommons.Composer', $this->controller->helpers);
		$this->assertFalse(in_array('NetCommons.Composer', $this->controller->helpers, true));

		$this->assertArrayHasKey('Pages.PageLayout', $this->controller->helpers);
	}

/**
 * Ajaxのテスト
 *
 * @return void
 */
	public function testBeforeRenderOnAjax() {
		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_component/index_json?frame_id=6', null, null, 'json');

		//チェック
		$this->assertArrayNotHasKey('page', $this->vars);
		$this->assertArrayNotHasKey('modal', $this->vars);
		$this->assertFalse(in_array('NetCommons.Composer', $this->controller->helpers, true));
		$this->assertFalse(in_array('Pages.PageLayout', $this->controller->helpers, true));
	}

/**
 * requestAction()のテスト
 *
 * @return void
 */
	public function testBeforeRenderOnRequestAction() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutComponentRequestAction');

		//ログイン
		TestAuthGeneral::login($this);

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_component_request_action/index?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('Controller/Component/TestPageLayoutComponent/index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '/' . preg_quote('Controller/Component/TestPageLayoutComponentRequestAction/index', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$this->assertArrayNotHasKey('page', $this->vars);
		$this->assertArrayNotHasKey('modal', $this->vars);
		$this->assertFalse(in_array('NetCommons.Composer', $this->controller->helpers, true));
		$this->assertFalse(in_array('Pages.PageLayout', $this->controller->helpers, true));
	}

}
