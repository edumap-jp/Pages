<?php
/**
 * ContainerFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ContainerFixture', 'Containers.Test/Fixture');

/**
 * ContainerFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Fixture
 */
class Container4pagesFixture extends ContainerFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Container';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'containers';

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Containers') . 'Config' . DS . 'Migration' . DS . '9999999999_container_records.php';
		$this->records = (new ContainerRecords())->records[$this->name];

		//パブリックスペースのホーム/test4
		$this->records[] = array('id' => '17', 'type' => '3');
		parent::init();
	}

}