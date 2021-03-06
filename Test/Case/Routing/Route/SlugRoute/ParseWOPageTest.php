<?php
/**
 * SlugRoute::parse()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsCakeTestCase', 'NetCommons.TestSuite');
App::uses('SlugRoute', 'Pages.Routing/Route');
App::uses('PageFixture', 'Pages.Test/Fixture');

/**
 * SlugRoute::parse()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Case\Routing\Route\SlugRoute
 */
class PagesRoutingRouteSlugRouteParseWOPageTestTest extends NetCommonsCakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.pages.page4drop',
	);

/**
 * Fixture merge
 *
 * @var array
 */
	protected $_isFixtureMerged = false;

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'pages';

/**
 * parse()のテスト
 *
 * @return void
 */
	public function testParse() {
		//$fixture = new PageFixture();
		//$db = ConnectionManager::getDataSource($fixture->useDbConfig);
		//$fixture->drop($db);
		//
		//$route = new SlugRoute('/*', array('plugin' => 'pages', 'controller' => 'pages', 'action' => 'index'));
		//
		//$route->compile();
		//$result = $route->parse('/');
		//$this->assertFalse($result);
		//
		//$fixture->create($db);
	}

}
