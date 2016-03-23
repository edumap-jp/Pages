<?php
/**
 * PageAssociationsBehavior::deleteContainers()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * PageAssociationsBehavior::deleteContainers()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Behavior\PageAssociationsBehavior
 */
class PageAssociationsBehaviorDeleteContainersTest extends NetCommonsModelTestCase {

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
		$this->Container = ClassRegistry::init('Containers.Container');
		$this->ContainersPage = ClassRegistry::init('Containers.ContainersPage');
	}

/**
 * deleteContainers()テストのDataProvider
 *
 * ### 戻り値
 *  - pageId ページID
 *
 * @return array データ
 */
	public function dataProvider() {
		$result[0] = array();
		$result[0]['pageId'] = '7';

		return $result;
	}

/**
 * deleteContainers()のテスト
 *
 * @param int $pageId ページID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteContainers($pageId) {
		$containerId = '17';

		//事前チェック
		$count = $this->Container->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $containerId),
		));
		$this->assertEquals(1, $count);

		$count = $this->ContainersPage->find('count', array(
			'recursive' => -1,
			'conditions' => array('page_id' => $pageId),
		));
		$this->assertEquals(5, $count);

		$count = $this->ContainersPage->find('count', array(
			'recursive' => -1,
			'conditions' => array('page_id' => $pageId, 'container_id' => $containerId),
		));
		$this->assertEquals(1, $count);

		//テスト実施
		$result = $this->TestModel->deleteContainers($pageId);

		//戻り値チェック
		$this->assertTrue($result);

		//データチェック
		$count = $this->TestModel->Container->find('count', array(
			'recursive' => -1,
			'conditions' => array('id' => $containerId),
		));
		$this->assertEquals(0, $count);

		$count = $this->TestModel->ContainersPage->find('count', array(
			'recursive' => -1,
			'conditions' => array('page_id' => $pageId),
		));
		$this->assertEquals(0, $count);
	}

/**
 * deleteContainers()のContainer->deleteAll()のExceptionErrorテスト
 *
 * @param int $pageId ページID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteContainersOnContainerExceptionError($pageId) {
		$this->_mockForReturnFalse('TestModel', 'Containers.Container', 'deleteAll');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->TestModel->deleteContainers($pageId);
	}

/**
 * deleteContainers()のContainersPage->deleteAll()のExceptionErrorテスト
 *
 * @param int $pageId ページID
 * @dataProvider dataProvider
 * @return void
 */
	public function testDeleteContainersOnContainersPageExceptionError($pageId) {
		$this->_mockForReturnFalse('TestModel', 'Containers.ContainersPage', 'deleteAll');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->TestModel->deleteContainers($pageId);
	}

}
