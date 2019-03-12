<?php
/**
 * Page::beforeSave()とafterSave()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesModelTestCase', 'Pages.TestSuite');
App::uses('Page4pagesFixture', 'Pages.Test/Fixture');

/**
 * Page::beforeSave()とafterSave()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Page
 */
class PageSaveTest extends PagesModelTestCase {

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
	protected $_methodName = 'save';

/**
 * テストデータ取得
 *
 * @return void
 */
	private function __data() {
		$model = $this->_modelName;

		//データ生成
		$data['Page'] = Hash::extract((new Page4pagesFixture())->records, '{n}[id=5]')[0];
		Current::write('Page', $data['Page']);

		$data['Page']['slug'] = 'test2_upd';
		$data['Page']['permalink'] = 'test2_upd';

		//テスト実施
		if ($this->$model->Behaviors->loaded('Pages.SavePage')) {
			//$this->$model->Behaviors->unload('Pages.SavePage');
		}
		if ($this->$model->Behaviors->loaded('Pages.PageAssociations')) {
			//$this->$model->Behaviors->unload('Pages.PageAssociations');
		}

		return $data;
	}

/**
 * save()のテスト
 *
 * @return void
 */
	public function testSave() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = $this->__data();

		//テスト実施
		$result = $this->$model->$methodName($data);

		//チェック
		$expected = $data;
		$expected['Page']['is_container_fluid'] = false;
		unset($result['Page']['modified']);
		$this->assertEquals($expected, $result);

		//子ページのpermalinkのチェック
		$child = $this->$model->find('first', array(
			'recursive' => -1,
			'fields' => array('id', 'permalink'),
			'conditions' => array(
				'Page.parent_id' => $data['Page']['id'],
			),
		));
		$this->assertEquals('9', $child['Page']['id']);
		$this->assertEquals('test2_upd/home', $child['Page']['permalink']);
	}

/**
 * ExceptionErrorテスト
 *
 * @return void
 */
	public function testOnExceptionError() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$data = $this->__data();
		$this->_mockForReturnFalse($model, $model, 'saveField');

		//テスト実施
		$this->setExpectedException('InternalErrorException');
		$this->$model->$methodName($data);
	}

}
