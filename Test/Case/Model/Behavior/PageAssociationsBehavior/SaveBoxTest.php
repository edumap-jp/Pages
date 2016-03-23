<?php
/**
 * PageAssociationsBehavior::saveBox()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * PageAssociationsBehavior::saveBox()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Behavior\PageAssociationsBehavior
 */
class PageAssociationsBehaviorSaveBoxTest extends NetCommonsModelTestCase {

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
		$this->TestModel = ClassRegistry::init('TestPages.TestPageAssociationsBehaviorModel');

		//事前チェック用
		$this->Box = ClassRegistry::init('Boxes.Box');
	}

/**
 * saveBox()テストのDataProvider
 *
 * ### 戻り値
 *  - page ページデータ
 *
 * @return array データ
 */
	public function dataProvider() {
		$result[0] = array();
		$result[0]['page'] = array(
			'Page' => array('id' => '99', 'room_id' => '9'),
			'Container' => array('id' => '88'),
			'Room' => array('space_id' => '2'),
		);

		return $result;
	}

/**
 * saveBox()のテスト
 *
 * @param array $page ページデータ
 * @dataProvider dataProvider
 * @return void
 */
	public function testSaveBox($page) {
		$boxId = '18';

		//事前チェック
		$count = $this->Box->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $boxId),
		));
		$this->assertEquals(0, $count);

		//テスト実施
		$result = $this->TestModel->saveBox($page);

		//戻り値チェック
		$this->assertDatetime($result['Box']['created']);
		$this->assertDatetime($result['Box']['modified']);

		unset($result['Box']['created']);
		unset($result['Box']['modified']);

		$expected = Hash::insert(array(), 'Box', array(
			'id' => $boxId,
			'type' => '4',
			'container_id' => Hash::get($page, 'Container.id'),
			'space_id' => Hash::get($page, 'Room.space_id'),
			'room_id' => Hash::get($page, 'Page.room_id'),
			'page_id' => Hash::get($page, 'Page.id'),
		));
		$this->assertEquals($expected, $result);

		//データチェック
		$count = $this->TestModel->Box->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $boxId),
		));
		$this->assertEquals(1, $count);
	}

/**
 * saveBox()のExceptionErrorテスト
 *
 * @param array $page ページデータ
 * @dataProvider dataProvider
 * @return void
 */
	public function testSaveBoxOnExceptionError($page) {
		$this->_mockForReturnFalse('TestModel', 'Boxes.Box', 'save');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->TestModel->saveBox($page);
	}

}
