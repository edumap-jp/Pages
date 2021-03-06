<?php
/**
 * PagesEditHelper::getLayouts()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesHelperTestCase', 'Pages.TestSuite');

/**
 * PagesEditHelper::getLayouts()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\View\Helper\PagesEditHelper
 */
class PagesEditHelperGetLayoutsTest extends PagesHelperTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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

		//テストデータ生成
		$viewVars = array();
		$requestData = array();
		$params = array();

		//Helperロード
		$this->loadHelper('Pages.PagesEdit', $viewVars, $requestData, $params);
	}

/**
 * getLayouts()のテスト
 *
 * @return void
 */
	public function testGetLayouts() {
		//テスト実施
		$result = $this->PagesEdit->getLayouts();

		//チェック
		$expected = array(
			0 => '0_0_0_0.png',
			1 => '0_0_0_1.png',
			2 => '0_0_1_0.png',
			3 => '0_0_1_1.png',
			4 => '0_1_0_0.png',
			5 => '0_1_0_1.png',
			6 => '0_1_1_0.png',
			7 => '0_1_1_1.png',
			8 => '1_0_0_0.png',
			9 => '1_0_0_1.png',
			10 => '1_0_1_0.png',
			11 => '1_0_1_1.png',
			12 => '1_1_0_0.png',
			13 => '1_1_0_1.png',
			14 => '1_1_1_0.png',
			15 => '1_1_1_1.png'
		);

		$this->assertEquals($expected, $result);
	}

}
