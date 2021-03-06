<?php
/**
 * FrameFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('FrameFixture', 'Frames.Test/Fixture');

/**
 * FrameFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Fixture
 */
class Frame4pagesFixture extends FrameFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Frame';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'frames';

/**
 * Records
 *
 * @var array
 */
	protected $_records = array(
		//ヘッダー
		array(
			'id' => '1',
			'room_id' => '2',
			'box_id' => '1',
			'plugin_key' => 'test_plugin',
			'block_id' => '2',
			'key' => 'frame_header',
			'weight' => '1',
			'is_deleted' => '0',
		),
		//レフト
		array(
			'id' => '2',
			'room_id' => '2',
			'box_id' => '2',
			'plugin_key' => 'test_plugin',
			'block_id' => '2',
			'key' => 'frame_major',
			'weight' => '1',
			'is_deleted' => '0',
		),
		//ライト
		array(
			'id' => '3',
			'room_id' => '2',
			'box_id' => '3',
			'plugin_key' => 'test_plugin',
			'block_id' => '2',
			'key' => 'frame_minor',
			'weight' => '1',
			'is_deleted' => '0',
		),
		//フッター
		array(
			'id' => '4',
			'room_id' => '2',
			'box_id' => '4',
			'plugin_key' => 'test_plugin',
			'block_id' => '2',
			'key' => 'frame_footer',
			'weight' => '1',
			'is_deleted' => '0',
		),
		//メイン
		array(
			'id' => '6',
			'room_id' => '2',
			'box_id' => '28',
			'plugin_key' => 'test_plugin',
			'block_id' => '2',
			'key' => 'frame_3',
			'weight' => '1',
			'is_deleted' => '0',
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		parent::init();
		$this->records = Hash::remove($this->records, '{n}[id=5]');
		$this->records = Hash::remove($this->records, '{n}[id=6]');
		$this->records = Hash::merge($this->records, $this->_records);

		$records = array_keys($this->records);
		foreach ($records as $i) {
			$this->records[$i]['plugin_key'] = 'test_pages';
		}
	}

}
