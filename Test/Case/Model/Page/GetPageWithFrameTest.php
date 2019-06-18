<?php
/**
 * Page::getPageWithFrame()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesGetTestCase', 'Pages.TestSuite');

/**
 * Page::getPageWithFrame()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\Page
 */
class PageGetPageWithFrameTest extends PagesGetTestCase {

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
	protected $_methodName = 'getPageWithFrame';

/**
 * getPageWithFrame()のテスト
 *
 * @return void
 * @see GetPageBehavior::getPageWithFrame()
 */
	public function testGetPageWithFrame() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$permalink = 'test4';

		//テスト実施
		$result = $this->$model->$methodName($permalink, '2');

		//チェック
		$expected = array('Page', 'Room', 'ParentPage', 'Space', 'PagesLanguage', 'PageContainer');
		$this->assertEquals($expected, array_keys($result));

		$this->__assertPage($result['Page'], array());
		$this->__assertPageContainers($result['PageContainer']);
	}

/**
 * getPageWithFrame()のテスト
 *
 * @return void
 */
	public function testGetPageWithFrameByRoot() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		Current::write('Room.page_id_top', '7');
		Current::write('Page', array(
			'id' => '7', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '4',
			//'lft' => '3', 'rght' => '4',
			'lft' => null, 'rght' => null,
			'weight' => '1',
			'sort_key' => '~00000001-00000001-00000001',
			'child_count' => '0',
			'permalink' => 'test4', 'slug' => 'test4',
		));
		Current::write('TopPage', array(
			'id' => '7',
			'permalink' => 'test4',
		));

		//データ生成
		$permalink = '';

		//テスト実施
		$result = $this->$model->$methodName($permalink, '2');

		//チェック
		$expected = array('Page', 'Room', 'ParentPage', 'Space', 'PagesLanguage', 'PageContainer');
		$this->assertEquals($expected, array_keys($result));

		$this->__assertPage($result['Page']);
		$this->__assertPageContainers($result['PageContainer']);
	}

/**
 * Pageのチェック
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPage($result) {
		$result = Hash::remove($result, 'created_user');
		$result = Hash::remove($result, 'created');
		$result = Hash::remove($result, 'modified_user');
		$result = Hash::remove($result, 'modified');

		$expected = array(
			'id' => '7', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '4',
			//'lft' => '3', 'rght' => '4',
			'lft' => null, 'rght' => null,
			'weight' => '1',
			'sort_key' => '~00000001-00000001-00000001',
			'child_count' => '0',
			'permalink' => 'test4', 'slug' => 'test4', 'is_container_fluid' => false, 'theme' => null,
			'full_permalink' => 'test4',
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Boxesのチェック
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainers($result) {
		$result = Hash::remove($result, '{n}.created_user');
		$result = Hash::remove($result, '{n}.created');
		$result = Hash::remove($result, '{n}.modified_user');
		$result = Hash::remove($result, '{n}.modified');

		$result = Hash::remove($result, '{n}.{s}.created_user');
		$result = Hash::remove($result, '{n}.{s}.created');
		$result = Hash::remove($result, '{n}.{s}.modified_user');
		$result = Hash::remove($result, '{n}.{s}.modified');

		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.created_user');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.created');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.modified_user');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.modified');

		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.{n}.created_user');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.{n}.created');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.{n}.modified_user');
		$result = Hash::remove($result, '{n}.{s}.{n}.{s}.{n}.modified');

		$this->assertCount(5, $result);

		$this->__assertPageContainerHeader($result[0]);
		$this->__assertPageContainerMajor($result[1]);
		$this->__assertPageContainerMain($result[2]);
		$this->__assertPageContainerMinor($result[3]);
		$this->__assertPageContainerFooter($result[4]);
	}

/**
 * Boxのチェック(レフト)
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainerMajor($result) {
		$expected = array(
			'id' => '32',
			'page_id' => '7',
			'container_type' => '2',
			'is_published' => true,
			'is_configured' => false,
			'Box' => array(
				0 => array(
					'BoxesPageContainer' => array(
						'id' => '67',
						'page_container_id' => '32',
						'page_id' => '7',
						'container_type' => '2',
						'box_id' => '2',
						'is_published' => true,
						'weight' => '1',
					),
					'Box' => array(
						'id' => '2',
						'container_id' => null,
						'type' => '1',
						'space_id' => '1',
						'room_id' => '1',
						'page_id' => null,
						'container_type' => '2',
						'weight' => null,
					),
					'Room' => array(
						'id' => '1',
						'space_id' => '1',
						'page_id_top' => null,
						'parent_id' => null,
						//'lft' => '1',
						//'rght' => '12',
						'weight' => '1',
						'sort_key' => '~00000001',
						'child_count' => '5',
						'active' => true,
						'in_draft' => false,
						'default_role_key' => 'visitor',
						'need_approval' => true,
						'default_participation' => true,
						'page_layout_permitted' => false,
						'theme' => null,
					),
					'RoomsLanguage' => array(
						'id' => null, 'name' => null,
					),
					'Frame' => array(
						0 => array(
							'id' => '2',
							'language_id' => '2',
							'room_id' => '2',
							'box_id' => '2',
							'plugin_key' => 'test_pages',
							'block_id' => '2',
							'key' => 'frame_major',
							'name' => 'Test frame major',
							'header_type' => 'default',
							'weight' => '1',
							'is_deleted' => false,
							'default_action' => '',
							'frame_id' => '2',
							'is_origin' => true,
							'is_translation' => false,
							'is_original_copy' => false,
							'default_setting_action' => ''
						),
					),
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Boxのチェック(フッター)
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainerFooter($result) {
		$expected = array(
			'id' => '35',
			'page_id' => '7',
			'container_type' => '5',
			'is_published' => true,
			'is_configured' => false,
			'Box' => array(
				0 => array(
					'BoxesPageContainer' => array(
						'id' => '76',
						'page_container_id' => '35',
						'page_id' => '7',
						'container_type' => '5',
						'box_id' => '4',
						'is_published' => true,
						'weight' => '1',
					),
					'Box' => array(
						'id' => '4',
						'container_id' => null,
						'type' => '1',
						'space_id' => '1',
						'room_id' => '1',
						'page_id' => null,
						'container_type' => '5',
						'weight' => null,
					),
					'Room' => array(
						'id' => '1',
						'space_id' => '1',
						'page_id_top' => null,
						'parent_id' => null,
						//'lft' => '1',
						//'rght' => '12',
						'weight' => '1',
						'sort_key' => '~00000001',
						'child_count' => '5',
						'active' => true,
						'in_draft' => false,
						'default_role_key' => 'visitor',
						'need_approval' => true,
						'default_participation' => true,
						'page_layout_permitted' => false,
						'theme' => null,
					),
					'RoomsLanguage' => array(
						'id' => null, 'name' => null,
					),
					'Frame' => array(
						0 => array(
							'id' => '4',
							'language_id' => '2',
							'room_id' => '2',
							'box_id' => '4',
							'plugin_key' => 'test_pages',
							'block_id' => '2',
							'key' => 'frame_footer',
							'name' => 'Test frame footer',
							'header_type' => 'default',
							'weight' => '1',
							'is_deleted' => false,
							'default_action' => '',
							'frame_id' => '4',
							'is_origin' => true,
							'is_translation' => false,
							'is_original_copy' => false,
							'default_setting_action' => ''
						),
					),
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Boxのチェック(メイン)
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainerMain($result) {
		$expected = array(
			'id' => '33',
			'page_id' => '7',
			'container_type' => '3',
			'is_published' => true,
			'is_configured' => false,
			'Box' => array(
				0 => array(
					'BoxesPageContainer' => array(
						'id' => '71',
						'page_container_id' => '33',
						'page_id' => '7',
						'container_type' => '3',
						'box_id' => '33',
						'is_published' => true,
						'weight' => '1',
					),
					'Box' => array(
						'id' => '33',
						'container_id' => null,
						'type' => '4',
						'space_id' => '2',
						'room_id' => '2',
						'page_id' => '7',
						'container_type' => '3',
						'weight' => null,
					),
					'Room' => array(
						'id' => '2',
						'space_id' => '2',
						'page_id_top' => '1',
						'parent_id' => '1',
						//'lft' => '2',
						//'rght' => '7',
						'weight' => '1',
						'sort_key' => '~00000001-00000001',
						'child_count' => '2',
						'active' => true,
						'in_draft' => false,
						'default_role_key' => 'visitor',
						'need_approval' => true,
						'default_participation' => true,
						'page_layout_permitted' => true,
						'theme' => null,
					),
					'RoomsLanguage' => array(
						'id' => '2', 'name' => 'Room name',
					),
					//'Frame' => array(),
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Boxのチェック(ヘッダー)
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainerHeader($result) {
		$expected = array(
			'id' => '31',
			'page_id' => '7',
			'container_type' => '1',
			'is_published' => true,
			'is_configured' => false,
			'Box' => array(
				0 => array(
					'BoxesPageContainer' => array(
						'id' => '63',
						'page_container_id' => '31',
						'page_id' => '7',
						'container_type' => '1',
						'box_id' => '1',
						'is_published' => true,
						'weight' => '1',
					),
					'Box' => array(
						'id' => '1',
						'container_id' => null,
						'type' => '1',
						'space_id' => '1',
						'room_id' => '1',
						'page_id' => null,
						'container_type' => '1',
						'weight' => null,
					),
					'Room' => array(
						'id' => '1',
						'space_id' => '1',
						'page_id_top' => null,
						'parent_id' => null,
						//'lft' => '1',
						//'rght' => '12',
						'weight' => '1',
						'sort_key' => '~00000001',
						'child_count' => '5',
						'active' => true,
						'in_draft' => false,
						'default_role_key' => 'visitor',
						'need_approval' => true,
						'default_participation' => true,
						'page_layout_permitted' => false,
						'theme' => null,
					),
					'RoomsLanguage' => array(
						'id' => null, 'name' => null
					),
					'Frame' => array(
						0 => array(
							'id' => '1',
							'language_id' => '2',
							'room_id' => '2',
							'box_id' => '1',
							'plugin_key' => 'test_pages',
							'block_id' => '2',
							'key' => 'frame_header',
							'name' => 'Test frame header',
							'header_type' =>
							'default',
							'weight' => '1',
							'is_deleted' => false,
							'default_action' => '',
							'frame_id' => '1',
							'is_origin' => true,
							'is_translation' => false,
							'is_original_copy' => false,
							'default_setting_action' => ''
						),
					),
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

/**
 * Boxのチェック(ライト)
 *
 * @param array $result 結果データ
 * @return void
 */
	private function __assertPageContainerMinor($result) {
		$expected = array(
			'id' => '34',
			'page_id' => '7',
			'container_type' => '4',
			'is_published' => true,
			'is_configured' => false,
			'Box' => array(
				0 => array(
					'BoxesPageContainer' => array(
						'id' => '72',
						'page_container_id' => '34',
						'page_id' => '7',
						'container_type' => '4',
						'box_id' => '3',
						'is_published' => true,
						'weight' => '1',
					),
					'Box' => array(
						'id' => '3',
						'container_id' => null,
						'type' => '1',
						'space_id' => '1',
						'room_id' => '1',
						'page_id' => null,
						'container_type' => '4',
						'weight' => null,
					),
					'Room' => array(
						'id' => '1',
						'space_id' => '1',
						'page_id_top' => null,
						'parent_id' => null,
						//'lft' => '1',
						//'rght' => '12',
						'weight' => '1',
						'sort_key' => '~00000001',
						'child_count' => '5',
						'active' => true,
						'in_draft' => false,
						'default_role_key' => 'visitor',
						'need_approval' => true,
						'default_participation' => true,
						'page_layout_permitted' => false,
						'theme' => null,
					),
					'RoomsLanguage' => array(
						'id' => null, 'name' => null,
					),
					'Frame' => array(
						0 => array(
							'id' => '3',
							'language_id' => '2',
							'room_id' => '2',
							'box_id' => '3',
							'plugin_key' => 'test_pages',
							'block_id' => '2',
							'key' => 'frame_minor',
							'name' => 'Test frame minor',
							'header_type' => 'default',
							'weight' => '1',
							'is_deleted' => false,
							'default_action' => '',
							'frame_id' => '3',
							'is_origin' => true,
							'is_translation' => false,
							'is_original_copy' => false,
							'default_setting_action' => ''
						),
					),
				),
			),
		);
		$this->assertEquals($expected, $result);
	}

}
