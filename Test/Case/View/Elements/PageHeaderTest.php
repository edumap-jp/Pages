<?php
/**
 * View/Elements/page_headerのテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * View/Elements/page_headerのテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\View\Elements\PageHeader
 */
class PagesViewElementsPageHeaderTest extends PagesControllerTestCase {

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
		$this->generateNc('TestPages.TestViewElementsPageHeader');
	}

/**
 * View/Elements/page_headerのテスト
 *
 * @return void
 */
	public function testPageHeader() {
		//テスト実行
		$this->_testGetAction('/test_pages/test_view_elements_page_header/page_header/2?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/page_header', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = '<header id="container-header">';
		$this->assertTextContains($pattern, $this->view);

		$pattern = 'test_pages/test_page/index';
		$this->assertTextContains($pattern, $this->view);
	}

/**
 * View/Elements/page_headerのテスト(PageLayoutHelperなし)
 *
 * @return void
 */
	public function testPageHeaderWOPageLayoutHelper() {
		//テスト実行
		$this->_testGetAction('/test_pages/test_view_elements_page_header/w_o_page_layout_helper/2?frame_id=6',
				array('method' => 'assertNotEmpty'), null, 'view');

		//チェック
		$pattern = '/' . preg_quote('View/Elements/page_header', '/') . '/';
		$this->assertRegExp($pattern, $this->view);

		$pattern = 'test_pages/test_page/index';
		$this->assertTextNotContains($pattern, $this->view);
	}

}
