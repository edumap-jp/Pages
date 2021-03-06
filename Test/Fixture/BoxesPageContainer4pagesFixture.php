<?php
/**
 * BoxFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('BoxesPageContainerFixture', 'Boxes.Test/Fixture');
App::uses('Box4pagesFixture', 'Pages.Test/Fixture');
App::uses('PageContainer4pagesFixture', 'Pages.Test/Fixture');

/**
 * BoxFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\Fixture
 */
class BoxesPageContainer4pagesFixture extends BoxesPageContainerFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'BoxesPageContainer';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'boxes_page_containers';

/**
 * ルームID
 *
 * @var array
 */
	protected $_roomId = array(
		'2' => array('2', '5', '6'),
	);

/**
 * ページID
 *
 * @var array
 */
	protected $_pageId = array(
		'2' => array('1', '4', '7', '8'),
		'5' => array('5', '9'),
		'6' => array('6'),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		$fixture = new Box4pagesFixture();
		$fixture->setRecords();
		$this->_boxes = $fixture->records;

		$fixture = new PageContainer4pagesFixture();
		$fixture->setRecords();
		$this->_pageContainers = $fixture->records;

		parent::init();
	}

}
