<?php
/**
 * Page::saveTheme()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesModelTestCase', 'Pages.TestSuite');

/**
 * Page::saveTheme()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Page
 */
class PageSaveThemeTest extends PagesModelTestCase {

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
	protected $_methodName = 'saveTheme';

/**
 * テストデータ生成
 *
 * @return array テストデータ
 */
	private function __data() {
		$data = array(
			'Page' => array('id' => '4', 'theme' => 'Default'),
		);
		return $data;
	}

/**
 * saveTheme()のテスト
 *
 * @return void
 */
	public function testSaveTheme() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		$data = $this->__data();

		//事前チェック
		$pageId = Hash::get($data, 'Page.id');
		$result = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $pageId),
		));
		$this->assertNull($result['Page']['theme']);

		//テスト実施
		$result = $this->$model->$methodName($data);
		$this->assertTrue($result);

		//チェック
		$result = $this->$model->find('first', array(
			'recursive' => -1,
			'conditions' => array('id' => $pageId),
		));
		$this->assertEquals('Default', $result['Page']['theme']);
	}

/**
 * saveTheme()のテスト(ページデータなし)
 *
 * @return void
 */
	public function testSaveThemeNotExists() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		$data = $this->__data();
		$data = Hash::insert($data, 'Page.id', '99');

		//テスト実施
		$result = $this->$model->$methodName($data);
		$this->assertFalse($result);
	}

/**
 * saveTheme()のExceptionErrorテスト
 *
 * @return void
 */
	public function testSaveThemeOnExceptionError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		$data = $this->__data();
		$this->_mockForReturnFalse($model, 'Pages.Page', 'saveField');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->$model->$methodName($data);
	}

}
