<?php
/**
 * SavePageBehavior::validates()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesModelTestCase', 'Pages.TestSuite');
App::uses('TestSavePageBehaviorValidatesModelFixture', 'Pages.Test/Fixture');

/**
 * SavePageBehavior::validates()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Behavior\SavePageBehavior
 */
class SavePageBehaviorValidatesTest extends PagesModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.test_save_page_behavior_validates_model',
		'plugin.pages.pages_language',
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
		$this->TestModel = ClassRegistry::init('TestPages.TestSavePageBehaviorValidatesModel');
	}

/**
 * validates()のテスト
 *
 * @return void
 */
	public function testValidates() {
		//テストデータ
		$data = array(
			'Page' => (new TestSavePageBehaviorValidatesModelFixture())->records[0],
			'PagesLanguage' => array('id' => '1'),
		);
		$this->_mockForReturnTrue('TestModel', 'Pages.PagesLanguage', 'validates');

		//テスト実施
		$this->TestModel->set($data);
		$result = $this->TestModel->validates();

		//チェック
		$this->assertTrue($result);
	}

/**
 * validates()のValidationErrorテスト
 *
 * @return void
 */
	public function testValidatesOnValidationError() {
		//テストデータ
		$data = array(
			'Page' => (new TestSavePageBehaviorValidatesModelFixture())->records[0],
			'PagesLanguage' => array('id' => '1', 'name' => ''),
		);

		//テスト実施
		$this->TestModel->set($data);
		$result = $this->TestModel->validates();

		//チェック
		$this->assertFalse($result);
		$expected = array('name' => array(
			sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name'))
		));
		$this->assertEquals($expected, $this->TestModel->validationErrors);
	}

/**
 * validates()のRoomIdなしテスト
 *
 * @return void
 */
	public function testValidatesWORoomId() {
		//テストデータ
		$data = array(
			'Page' => (new TestSavePageBehaviorValidatesModelFixture())->records[0],
			'PagesLanguage' => array('id' => '1'),
		);
		$data = Hash::remove($data, 'Page.room_id');
		Current::write('Room.id', '3');
		$this->_mockForReturnTrue('TestModel', 'Pages.PagesLanguage', 'validates');

		//テスト実施
		$this->TestModel->set($data);
		$result = $this->TestModel->validates();

		//チェック
		$this->assertTrue($result);
		$this->assertEquals('3', $this->TestModel->data['Page']['room_id']);
	}

}
