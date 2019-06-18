<?php
/**
 * Page::getPages()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesGetTestCase', 'Pages.TestSuite');

/**
 * Page::getPages()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Page
 */
class PageGetPagesTest extends PagesGetTestCase {

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
	protected $_methodName = 'getPages';

/**
 * getPages()のテスト
 *
 * @return void
 */
	public function testGetPages() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$roomIds = '5';

		//テスト実施
		$result = $this->$model->$methodName($roomIds);

		//チェック
		$this->assertCount(2, $result);

		$this->__assertGetPages($result,
			array('id' => '5', 'permalink' => 'test2'),
			array('id' => '5', 'parent_id' => '2'),
			array('id' => '3', 'room_id' => '4', 'parent_id' => '1'),
			array(
				array('id' => '9', 'room_id' => '5', 'parent_id' => '5', 'permalink' => 'test2/home'),
			)
		);
	}

/**
 * getPages()のテスト(引数省略)
 *
 * @return void
 */
	public function testGetPagesWOArgument() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		Current::write('Room.id', '5');

		//テスト実施
		$result = $this->$model->$methodName();

		//チェック
		$this->assertCount(2, $result);

		$this->__assertGetPages($result,
			array('id' => '5', 'permalink' => 'test2'),
			array('id' => '5', 'parent_id' => '2'),
			array('id' => '3', 'room_id' => '4', 'parent_id' => '1'),
			array(
				array('id' => '9', 'room_id' => '5', 'parent_id' => '5', 'permalink' => 'test2/home'),
			)
		);
	}

/**
 * getPages()のテスト
 *
 * @return void
 */
	public function testGetPagesByArray() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$roomIds = array('2', '5');

		//テスト実施
		$result = $this->$model->$methodName($roomIds);

		//チェック
		$this->assertCount(6, $result);

		$this->__assertGetPages($result,
			array('id' => '1', 'permalink' => ''),
			array('id' => '2', 'parent_id' => '1'),
			array('id' => null, 'room_id' => null, 'parent_id' => null),
			array(
				array('id' => '4', 'room_id' => '2', 'parent_id' => '1', 'permalink' => 'home'),
				array('id' => '8', 'room_id' => '2', 'parent_id' => '1', 'permalink' => 'test5'),
			)
		);
		$this->__assertGetPages($result,
			array('id' => '4', 'permalink' => 'home'),
			array('id' => '2', 'parent_id' => '1'),
			array('id' => '1', 'room_id' => '2', 'parent_id' => null),
			array(
				// homeページ下ページなら、permalinkは home/test4 になるようバグ修正したため、テストケースも修正
				//array('id' => '7', 'room_id' => '2', 'parent_id' => '4', 'permalink' => 'test4')
				array('id' => '7', 'room_id' => '2', 'parent_id' => '4', 'permalink' => 'home/test4')
			)
		);
		$this->__assertGetPages($result,
			// homeページ下ページなら、permalinkは home/test4 になるようバグ修正したため、テストケースも修正
			//array('id' => '7', 'permalink' => 'test4'),
			array('id' => '7', 'permalink' => 'home/test4'),
			array('id' => '2', 'parent_id' => '1'),
			array('id' => '4', 'room_id' => '2', 'parent_id' => '1'),
			array()
		);
		$this->__assertGetPages($result,
			array('id' => '8', 'permalink' => 'test5'),
			array('id' => '2', 'parent_id' => '1'),
			array('id' => '1', 'room_id' => '2', 'parent_id' => null),
			array()
		);
		$this->__assertGetPages($result,
			array('id' => '5', 'permalink' => 'test2'),
			array('id' => '5', 'parent_id' => '2'),
			array('id' => '3', 'room_id' => '4', 'parent_id' => '1'),
			array(
				array('id' => '9', 'room_id' => '5', 'parent_id' => '5', 'permalink' => 'test2/home'),
			)
		);
	}

/**
 * getPages()のチェック
 *
 * @param array $result 結果
 * @param array $expectedPage ページの期待値
 * @param array $expectedRoom ルームの期待値
 * @param array $expectedParentPage 親ページの期待値
 * @param array $expectedChildPage 子ページの期待値
 * @return void
 */
	private function __assertGetPages($result, $expectedPage, $expectedRoom, $expectedParentPage, $expectedChildPage) {
		$pageId = $expectedPage['id'];

		$expected = array(
			'Page', 'Room', 'Space', 'ChildPage', 'PagesLanguage',
		);
		$this->assertEquals($expected, array_keys($result[$pageId]));

		$this->assertEquals($expectedPage['id'], Hash::get($result[$pageId], 'Page.id'));
		$this->assertEquals($expectedRoom['id'], Hash::get($result[$pageId], 'Page.room_id'));
		$this->assertEquals($expectedParentPage['id'], Hash::get($result[$pageId], 'Page.parent_id'));
		$this->assertEquals($expectedPage['permalink'], Hash::get($result[$pageId], 'Page.permalink'));

		$this->assertEquals($expectedRoom['id'], Hash::get($result[$pageId], 'Room.id'));
		$this->assertEquals($expectedRoom['parent_id'], Hash::get($result[$pageId], 'Room.parent_id'));

		//$this->assertEquals($expectedParentPage['id'], Hash::get($result[$pageId], 'ParentPage.id'));
		//$this->assertEquals($expectedParentPage['room_id'], Hash::get($result[$pageId], 'ParentPage.room_id'));

		$this->assertEquals($expectedPage['id'], Hash::get($result[$pageId], 'PagesLanguage.page_id'));

		$this->assertCount(count($expectedChildPage), Hash::get($result[$pageId], 'ChildPage'));
		if (count($expectedChildPage) > 0) {
			$index = 0;
			$this->assertEquals(
				$expectedChildPage[$index]['id'], Hash::get($result[$pageId], 'ChildPage.' . $index . '.id')
			);
			$this->assertEquals(
				$expectedChildPage[$index]['room_id'], Hash::get($result[$pageId], 'ChildPage.' . $index . '.room_id')
			);
			$this->assertEquals(
				$expectedChildPage[$index]['parent_id'], Hash::get($result[$pageId], 'ChildPage.' . $index . '.parent_id')
			);
			$this->assertEquals(
				$expectedChildPage[$index]['permalink'], Hash::get($result[$pageId], 'ChildPage.' . $index . '.permalink')
			);
		}
	}

}
