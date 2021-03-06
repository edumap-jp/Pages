<?php
/**
 * PageLayoutHelper::beforeLayout()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PageLayoutHelper::beforeLayout()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Controller\Component\PageLayoutHelper
 */
class PageLayoutHelperBeforeLayoutTest extends PagesControllerTestCase {

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
 * beforeLayout()のテスト
 *
 * @return void
 */
	public function testBeforeLayout() {
		//テストコントローラ生成
		$this->generateNc('TestPages.TestPageLayoutHelperBeforeLayout');

		//テスト実行
		$this->_testGetAction('/test_pages/test_page_layout_helper_before_layout/index/2?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Helper/TestPageLayoutHelperBeforeLayout', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$View = $this->controller->View->PageLayout->_View;
		$this->assertTextContains('test_pages/test_page/index', $View->viewVars['pageHeader']);
		$this->assertTextContains('test_pages/test_page/index', $View->viewVars['pageMajor']);
		$this->assertTextContains('test_pages/test_page/index', $View->viewVars['pageMinor']);
		$this->assertTextContains('test_pages/test_page/index', $View->viewVars['pageFooter']);
	}

}
