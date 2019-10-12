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
class Page4pagesFixture extends PageFixture {

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
		//パブリックスペースのページ（使われることはない）
		array(
			'id' => '1', 'room_id' => '2', 'root_id' => null,
			'parent_id' => null,
			//'lft' => '1', 'rght' => '8',
			'weight' => '1',
			'sort_key' => '~00000001',
			'child_count' => '3',
			'permalink' => '', 'slug' => null,
		),
		//パブリックスペースのホーム
		array(
			'id' => '4', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '2', 'rght' => '5',
			'weight' => '1',
			'sort_key' => '~00000001-00000001',
			'child_count' => '1',
			'permalink' => 'home', 'slug' => 'home',
		),
		//ホーム/test4
		array(
			'id' => '7', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '4',
			//'lft' => '3', 'rght' => '4',
			'weight' => '1',
			'sort_key' => '~00000001-00000001-00000001',
			'child_count' => '0',
			'permalink' => 'home/test4', 'slug' => 'test4',
		),
		//パブリックスペースのtest5
		array(
			'id' => '8', 'room_id' => '2', 'root_id' => '1',
			'parent_id' => '1',
			//'lft' => '6', 'rght' => '7',
			'weight' => '2',
			'sort_key' => '~00000001-00000002',
			'child_count' => '0',
			'permalink' => 'test5', 'slug' => 'test5',
		),
		//プライベートスペースのページ（使われることはない）
		array(
			'id' => '2', 'room_id' => '3', 'root_id' => null,
			'parent_id' => null,
			//'lft' => '9', 'rght' => '12',
			'weight' => '2',
			'sort_key' => '~00000002',
			'child_count' => '1',
			'permalink' => '', 'slug' => null,
		),
		//管理者のプライベートページ
		array(
			'id' => '10', 'room_id' => '7', 'root_id' => '2',
			'parent_id' => '2',
			//'lft' => '10', 'rght' => '11',
			'weight' => '1',
			'sort_key' => '~00000002-00000001',
			'child_count' => '0',
			'permalink' => 'user_administrator', 'slug' => 'user_administrator',
		),
		//グループスペースのページ（使われることはない）
		array(
			'id' => '3', 'room_id' => '4', 'root_id' => null,
			'parent_id' => null,
			//'lft' => '13', 'rght' => '20',
			'weight' => '3',
			'sort_key' => '~00000003',
			'child_count' => '3',
			'permalink' => '', 'slug' => null,
		),
		//別ルーム(room_id=4)
		array(
			'id' => '5', 'room_id' => '5', 'root_id' => '3',
			'parent_id' => '3',
			//'lft' => '14', 'rght' => '17',
			'weight' => '1',
			'sort_key' => '~00000003-00000001',
			'child_count' => '1',
			'permalink' => 'test2', 'slug' => 'test2',
		),
		array(
			'id' => '9', 'room_id' => '5', 'root_id' => '3',
			'parent_id' => '5',
			//'lft' => '15', 'rght' => '16',
			'weight' => '1',
			'sort_key' => '~00000003-00000001-00000001',
			'child_count' => '0',
			'permalink' => 'test2/home', 'slug' => 'home',
		),
		//別ルーム(room_id=5、ブロックなし)
		array(
			'id' => '6', 'room_id' => '6', 'root_id' => '3',
			'parent_id' => '3',
			//'lft' => '18', 'rght' => '19',
			'weight' => '2',
			'sort_key' => '~00000003-00000002',
			'child_count' => '0',
			'permalink' => 'test3', 'slug' => 'test3',
		),
	);

}
