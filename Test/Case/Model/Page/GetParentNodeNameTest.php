<?php
/**
 * Page::getParentNodeName()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsGetTest', 'NetCommons.TestSuite');

/**
 * Page::getParentNodeName()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Page
 */
class PageGetParentNodeNameTest extends NetCommonsGetTest {

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
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'Page';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getParentNodeName';

/**
 * getParentNodeName()のテスト
 *
 * @return void
 */
	public function testGetParentNodeName() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$pageId = '7';

		//テスト実施
		$result = $this->$model->$methodName($pageId);

		//チェック
		$this->assertEquals(array('4' => 'Home ja', '7' => 'Test page 4'), $result);
	}

}