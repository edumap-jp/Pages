<?php
/**
 * PagesLanguage::getPagesLanguage()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesGetTestCase', 'Pages.TestSuite');

/**
 * PagesLanguage::getPagesLanguage()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Model\PagesLanguage
 */
class PagesLanguageGetPagesLanguageTest extends PagesGetTestCase {

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
	protected $_modelName = 'PagesLanguage';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'getPagesLanguage';

/**
 * getPagesLanguage()のテスト
 *
 * @return void
 */
	public function testGetPagesLanguage() {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$pageId = '7';
		$languageId = '2';

		//テスト実施
		$result = $this->$model->$methodName($pageId, $languageId);
		$result = Hash::remove($result, '{s}.created_user');
		$result = Hash::remove($result, '{s}.created');
		$result = Hash::remove($result, '{s}.modified_user');
		$result = Hash::remove($result, '{s}.modified');

		//チェック
		$expected = array(
			'PagesLanguage' => array(
				'id' => '10', 'page_id' => '7', 'language_id' => '2', 'name' => 'Test page 4',
				'meta_title' => null, 'meta_description' => null, 'meta_keywords' => null, 'meta_robots' => null,
				'is_origin' => true, 'is_translation' => false,
			),
			'Page' => array(
				'id' => '7', 'room_id' => '2', 'root_id' => '1',
				'parent_id' => '4',
				//'lft' => '3', 'rght' => '4',
				'lft' => null, 'rght' => null,
				'weight' => '1',
				'sort_key' => '~00000001-00000001-00000001',
				'child_count' => '0',
				'permalink' => 'test4', 'slug' => 'test4', 'is_container_fluid' => false, 'theme' => null,
			),
			'Language' => array(
				'id' => '2', 'code' => 'ja', 'weight' => '2', 'is_active' => true,
			)
		);
		$this->assertEquals($expected, $result);
	}

}
