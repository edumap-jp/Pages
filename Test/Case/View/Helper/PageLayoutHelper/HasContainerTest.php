<?php
/**
 * PageLayoutHelper::hasContainer()のテスト
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
 * PageLayoutHelper::hasContainer()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\View\Helper\PageLayoutHelper
 */
class PageLayoutHelperHasContainerTest extends PagesHelperTestCase {

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

		// homeページ下ページなら、permalinkは home/test4 になるようバグ修正したため、テストケースも修正
		//$viewVars['page'] = $this->Page->getPageWithFrame('test4', '2');
		$viewVars['page'] = $this->Page->getPageWithFrame('home/test4', '2');
		$this->loadHelper('Pages.PageLayout', $viewVars, $requestData, $params);

		$this->PageLayout->containers = Hash::combine(
			Hash::get($this->PageLayout->_View->viewVars, 'page.PageContainer', array()), '{n}.container_type', '{n}'
		);
		$this->PageLayout->plugins = Hash::combine(Current::read('PluginsRoom', array()), '{n}.Plugin.key', '{n}.Plugin');
	}

/**
 * hasContainer()のテスト
 *
 * @return void
 */
	public function testHasContainer() {
		//データ生成
		$containerType = Container::TYPE_MAJOR;

		//テスト実施
		$result = $this->PageLayout->hasContainer($containerType);

		//チェック
		$this->assertTrue($result);
	}

/**
 * hasContainer()のテスト(Frameなし)
 *
 * @return void
 */
	public function testHasContainerWOFrame() {
		//データ生成
		$containerType = Container::TYPE_MAIN;

		//テスト実施
		$result = $this->PageLayout->hasContainer($containerType);

		//チェック
		$this->assertFalse($result);
	}

/**
 * hasContainer()のテスト(ContainersPage.is_published=false)
 *
 * @return void
 */
	public function testHasContainerNotIsPublished() {
		//データ生成
		$containerType = Container::TYPE_MAJOR;
		$this->PageLayout->containers = Hash::insert(
			$this->PageLayout->containers, $containerType . '.is_published', false
		);

		//テスト実施
		$result = $this->PageLayout->hasContainer($containerType);

		//チェック
		$this->assertFalse($result);
	}

/**
 * hasContainer()のテスト(Frameなし、セッティングモードON)
 *
 * @return void
 */
	public function testHasContainerWOFrameOnSettingMode() {
		//データ生成
		$containerType = Container::TYPE_MAIN;
		Current::setSettingMode(true);

		//テスト実施
		$result = $this->PageLayout->hasContainer($containerType);

		//チェック
		$this->assertTrue($result);
		Current::setSettingMode(false);
	}

}
