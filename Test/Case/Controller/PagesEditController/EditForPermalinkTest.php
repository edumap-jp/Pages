<?php
/**
 * ForPermalinkTest
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('PagesControllerTestCase', 'Pages.TestSuite');

/**
 * PagesEditControllerEditForPermalinkTest
 *
 */
class PagesEditControllerEditForPermalinkTest extends PagesControllerTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'pages';

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'pages_edit';

/**
 * Constructs
 *
 * @param string $name The name parameter on PHPUnit_Framework_TestCase::__construct()
 * @param array  $data The data parameter on PHPUnit_Framework_TestCase::__construct()
 * @param string $dataName The dataName parameter on PHPUnit_Framework_TestCase::__construct()
 * @return void
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);

		$key = array_search('plugin.pages.page4pages', $this->fixtures);
		unset($this->fixtures[$key]);
		$key = array_search('plugin.rooms.room', $this->fixtures);
		unset($this->fixtures[$key]);

		$this->fixtures[] = 'plugin.pages.Page4PageEditController';
		$this->fixtures[] = 'plugin.pages.Room4PageEditController';
	}

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		TestAuthGeneral::login($this);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		TestAuthGeneral::logout($this);
		parent::tearDown();
	}

/**
 * testTopPage
 *
 * @return void
 */
	public function testTopPage() {
		$result = $this->testAction('/pages/pages_edit/edit/5/5', ['return' => 'vars']);

		$this->assertEquals('/', $result['parentPermalink']);
	}

/**
 * testNest
 *
 * @return void
 */
	public function testNest() {
		$result = $this->testAction('/pages/pages_edit/edit/5/9', ['return' => 'vars']);

		$this->assertEquals('/test2/', $result['parentPermalink']);
	}

/**
 * testNotPublic
 *
 * @return void
 */
	public function testNotPublic() {
		Current::write('Space.permalink', 'dummy');
		$result = $this->testAction('/pages/pages_edit/edit/5/9', ['return' => 'vars']);

		// Current::read('Space.permalink')が空の場合を考慮。空だと//test2/になり変なテストケースになっていたため、
		// 空確認のif文追加
		if (Current::read('Space.permalink')) {
			$expected = '/' .
				Current::read('Space.permalink') . '/' .
				'test2/';
		} else {
			$expected = '/test2/';
		}
		$this->assertEquals($expected, $result['parentPermalink']);

		Current::$current = [];
	}

}
