<?php
/**
 * PageLayoutHelper::containerSize()のテスト
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
 * PageLayoutHelper::containerSize()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\View\Helper\PageLayoutHelper
 */
class PageLayoutHelperContainerSizeTest extends PagesHelperTestCase {

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

		$viewVars['page'] = $this->Page->getPageWithFrame('home/test4', '2');
		$this->loadHelper('Pages.PageLayout', $viewVars, $requestData, $params);

		$this->PageLayout->containers = Hash::combine(
			Hash::get($this->PageLayout->_View->viewVars, 'page.PageContainer', array()), '{n}.container_type', '{n}'
		);
		$this->PageLayout->plugins = Hash::combine(Current::read('PluginsRoom', array()), '{n}.Plugin.key', '{n}.Plugin');
	}

/**
 * DataProvider
 *
 * ### 戻り値
 *  - containerType Containerタイプ
 *  - hasContainer Containerを持っていない(非表示)
 *  - expected 期待値
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			// * hasContainer=true
			array('containerType' => Container::TYPE_MAJOR, 'hasContainer' => true, 'expected' => 'col-md-3 col-md-pull-6'),
			array('containerType' => Container::TYPE_MINOR, 'hasContainer' => true, 'expected' => 'col-md-3'),
			array('containerType' => Container::TYPE_MAIN, 'hasContainer' => true, 'expected' => 'col-md-6 col-md-push-3'),
			array('containerType' => null, 'hasContainer' => true, 'expected' => 'col-md-6 col-md-push-3'),
			// * hasContainer=false
			array('containerType' => Container::TYPE_MAJOR, 'hasContainer' => false, 'expected' => ''),
			array('containerType' => Container::TYPE_MINOR, 'hasContainer' => false, 'expected' => ''),
			array('containerType' => Container::TYPE_MAIN, 'hasContainer' => false, 'expected' => 'col-md-9'),
			array('containerType' => null, 'hasContainer' => false, 'expected' => 'col-md-9'),
		);
	}

/**
 * containerSize()のテスト
 *
 * @param string $containerType Containerタイプ
 * @param string $hasContainer Containerを持っていない(非表示)
 * @param string $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testContainerSize($containerType, $hasContainer, $expected) {
		if (! $containerType || $containerType === Container::TYPE_MAIN) {
			if (! $hasContainer) {
				unset($this->PageLayout->containers[Container::TYPE_MAJOR]);
			}
		} else {
			if (! $hasContainer) {
				unset($this->PageLayout->containers[$containerType]);
			}
		}

		//テスト実施
		$result = $this->PageLayout->containerSize($containerType);

		//チェック
		$this->assertEquals($expected, $result);
	}

}
