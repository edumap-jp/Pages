<?php
/**
 * View/Elements/page_footerテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * View/Elements/page_footerテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\test_app\Plugin\TestPages\Controller
 */
class TestViewElementsPageFooterController extends AppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Pages.PageLayout',
	);

/**
 * page_footer
 *
 * @return void
 */
	public function page_footer() {
		$this->autoRender = true;
	}

/**
 * ページレイアウトヘルパー無し
 *
 * @return void
 */
	public function w_o_page_layout_helper() {
		$this->autoRender = true;
		$this->view = 'page_footer';
		$this->Components->unload('Pages.PageLayout');
	}

}
