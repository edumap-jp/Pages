<?php
/**
 * Unitテスト用Fixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PageFixture', 'Pages.Test/Fixture');

/**
 * Unitテスト用Fixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Fixture
 */
class Page4routesFixture extends PageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Page';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'pages';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		// * パブリックスペースのページ（使われることはない）
		array(
			'id' => '1', 'room_id' => '2', 'root_id' => null,
			'parent_id' => null,
			//'lft' => '1', 'rght' => '8',
			'weight' => '1',
			'sort_key' => '~00000001',
			'child_count' => '7',
			'permalink' => '', 'slug' => null,
		),
		// * パブリックスペースのホーム
		array(
			'id' => '4', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '2', 'rght' => '5',
			'weight' => '1',
			'sort_key' => '~00000001-00000001',
			'child_count' => '1',
			'permalink' => 'home', 'slug' => 'home',
		),
		// * ホーム/test4
		array(
			'id' => '7', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '4',
			//'lft' => '3', 'rght' => '4',
			'weight' => '1',
			'sort_key' => '~00000001-00000001-00000001',
			'child_count' => '0',
			'permalink' => 'test4', 'slug' => 'test4',
		),
		// * test_err
		array(
			'id' => '8', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '2',
			'sort_key' => '~00000001-00000002',
			'child_count' => '0',
			'permalink' => 'test_err', 'slug' => 'test_err',
		),
		// * test_err/test_error
		array(
			'id' => '9', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '3',
			'sort_key' => '~00000001-00000003',
			'child_count' => '0',
			'permalink' => 'test_err/test_error', 'slug' => 'test_err/test_error',
		),
		// * test_err/test_error/index
		array(
			'id' => '10', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '4',
			'sort_key' => '~00000001-00000004',
			'child_count' => '0',
			'permalink' => 'test_err/test_error/index', 'slug' => 'test_err/test_error/index',
		),
		// * test_error
		array(
			'id' => '11', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '5',
			'sort_key' => '~00000001-00000005',
			'child_count' => '0',
			'permalink' => 'test_error', 'slug' => 'test_error',
		),
		// * test_error/index
		array(
			'id' => '12', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '6',
			'sort_key' => '~00000001-00000006',
			'child_count' => '0',
			'permalink' => 'test_error/index', 'slug' => 'test_error/index',
		),
	);

}
