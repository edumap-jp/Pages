<?php
/**
 * PageLayoutHelper::getBox()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesHelperTestCase', 'Pages.TestSuite');
App::uses('Container', 'Containers.Model');

/**
 * PageLayoutHelper::getBox()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\View\Helper\PageLayoutHelper
 */
class PageLayoutHelperGetBoxTest extends PagesHelperTestCase {

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

		$this->Page = ClassRegistry::init('Pages.Page');
		$this->PluginsRoom = ClassRegistry::init('PluginManager.PluginsRoom');

		//テストデータ生成
		$result = $this->PluginsRoom->getPlugins('1', '2');
		Current::write('PluginsRoom', $result);

		//Helperロード
		$viewVars = array();
		$requestData = array();
		$params = array();

		$viewVars['page'] = $this->Page->getPageWithFrame('home', '2');
		$this->loadHelper('Pages.PageLayout', $viewVars, $requestData, $params);

		$this->PageLayout->containers = Hash::combine(
			Hash::get($this->PageLayout->_View->viewVars, 'page.PageContainer', array()), '{n}.container_type', '{n}'
		);
		$this->PageLayout->plugins = Hash::combine(Current::read('PluginsRoom', array()), '{n}.Plugin.key', '{n}.Plugin');
	}

/**
 * getBox()のテスト
 *
 * @return void
 */
	public function testGetBox() {
		//データ生成
		$containerType = Container::TYPE_MAIN;

		//テスト実施
		$result = $this->PageLayout->getBox($containerType);

		//チェック
		$this->assertArrayHasKey('Frame', $result[0]);
	}

/**
 * getBox()のテスト(Containerなし)
 *
 * @return void
 */
	public function testGetBoxWOContainer() {
		//データ生成
		$containerType = Container::TYPE_MAIN;

		//テスト実施
		$this->PageLayout->containers = array();
		$result = $this->PageLayout->getBox($containerType);

		//チェック
		$this->assertEquals(array(), $result);
	}

}
