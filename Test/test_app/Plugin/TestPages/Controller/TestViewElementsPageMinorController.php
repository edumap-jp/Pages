<?php
/**
 * View/Elements/page_minorテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/page_minorテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\test_app\Plugin\TestPages\Controller
 */
class TestViewElementsPageMinorController extends AppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Pages.PageLayout',
	);

/**
 * page_minor
 *
 * @return void
 */
	public function page_minor() {
		$this->autoRender = true;
	}

/**
 * ページレイアウトヘルパー無し
 *
 * @return void
 */
	public function w_o_page_layout_helper() {
		$this->autoRender = true;
		$this->view = 'page_minor';
		$this->Components->unload('Pages.PageLayout');
	}

}
