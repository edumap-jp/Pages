<?php
/**
 * PageLayoutHelper::afterRender()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');

/**
 * PageLayoutHelper::afterRender()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\Component\PageLayoutHelper
 */
class PageLayoutHelperAfterRenderTest extends NetCommonsControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.box4pages',
		'plugin.pages.boxes_page4pages',
		'plugin.pages.container4pages',
		'plugin.pages.containers_page4pages',
		'plugin.pages.frame4pages',
		'plugin.pages.languages_page4pages',
		'plugin.pages.page4pages',
		'plugin.pages.plugin4pages',
		'plugin.pages.plugins_room4pages',
	);

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
 * afterRender()のテスト
 *
 * @return void
 */
	public function testAfterRender() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutHelperAfterRender');

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_helper_after_render/index',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$View = $this->controller->View->PageLayout->_View;
		$this->assertEquals('Pages.default', $View->layout);

		$this->assertTextContains('View/Helper/TestPageLayoutHelperAfterRender', $View->viewVars['pageContent']);
		$this->assertTextContains('<section', $View->viewVars['pageContent']);

		$this->assertFalse($View->viewVars['isSettingMode']);
		$this->assertEquals('container', $View->viewVars['pageContainerCss']);
	}

/**
 * afterRender()のテスト(ページプラグイン)
 *
 * @return void
 */
	public function testAfterRenderAsPluginPage() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutHelperAfterRender');

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_helper_after_render/index_as_page',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$View = $this->controller->View->PageLayout->_View;
		$this->assertEquals('Pages.default', $View->layout);

		$this->assertTextContains('View/Helper/TestPageLayoutHelperAfterRender', $View->viewVars['pageContent']);
		$this->assertTextNotContains('<section', $View->viewVars['pageContent']);

		$this->assertFalse($View->viewVars['isSettingMode']);
		$this->assertEquals('container', $View->viewVars['pageContainerCss']);
	}

/**
 * afterRender()のテスト(Page.is_container_fluid=true：成り行き)
 *
 * @return void
 */
	public function testAfterRenderWIsContainerFluid() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutHelperAfterRender');

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_helper_after_render/index_is_container_fluid',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$View = $this->controller->View->PageLayout->_View;
		$this->assertEquals('Pages.default', $View->layout);

		$this->assertTextContains('View/Helper/TestPageLayoutHelperAfterRender', $View->viewVars['pageContent']);
		$this->assertTextContains('<section', $View->viewVars['pageContent']);

		$this->assertFalse($View->viewVars['isSettingMode']);
		$this->assertEquals('container-fluid', $View->viewVars['pageContainerCss']);
	}

/**
 * afterRender()のテスト(Frame編集ON)
 *
 * @return void
 */
	public function testAfterRenderOnFrameSetting() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutHelperAfterRender');

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_helper_after_render/index_on_frame_setting?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$View = $this->controller->View->PageLayout->_View;
		$this->assertEquals('Pages.default', $View->layout);

		$this->assertTextContains('View/Helper/TestPageLayoutHelperAfterRender', $View->viewVars['pageContent']);
		$this->assertTextContains('<section', $View->viewVars['pageContent']);
		$this->assertInput('form', null, '/frames/frames/edit', $View->viewVars['pageContent']);

		$this->assertTrue($View->viewVars['isSettingMode']);
		$this->assertEquals('container', $View->viewVars['pageContainerCss']);
	}

}
