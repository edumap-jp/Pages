<?php
/**
 * ページ表示 Controller
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('PagesAppController', 'Pages.Controller');
App::uses('Space', 'Rooms.Model');
App::uses('NetCommonsUrl', 'NetCommons.Utility');

/**
 * ページ表示 Controller
 *
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Controller
 */
class PagesController extends PagesAppController {

/**
 * 使用するModels
 *
 * - [Pages.Page](../../Pages/classes/Page.html)
 *
 * @var array
 */
	public $uses = array(
		'Pages.Page',
		'Rooms.Space',
	);

/**
 * 使用するComponents
 *
 * - [Pages.PageLayoutComponent](../../Pages/classes/PageLayoutComponent.html)
 *
 * @var array
 */
	public $components = array(
		'Pages.PageLayout',
	);

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeFilter() {
		//CurrentPage::__getPageConditionsでページ表示として扱う
		if ($this->params['action'] === 'index') {
			$this->request->params['pageView'] = true;
		}
		parent::beforeFilter();
	}

/**
 * index method
 *
 * @throws NotFoundException
 * @return void
 */
	public function index() {
		if (Current::isSettingMode() && ! Current::permission('page_editable')) {
			$paths = $this->params->params['pass'];
			$path = implode('/', $paths);
			Current::isSettingMode(false);
			return $this->redirect('/' . $path);
		}

		//ページデータの取得
		$paths = $this->params->params['pass'];
		$path = implode('/', $paths);

		$spacePermalink = isset($this->request->params['spacePermalink'])
			? $this->request->params['spacePermalink']
			: '';
		$space = $this->Space->find('first', array(
			'recursive' => -1,
			'conditions' => array('permalink' => $spacePermalink, 'id !=' => Space::WHOLE_SITE_ID)
		));

		$page = $this->Page->getPageWithFrame($path, $space['Space']['id']);
		if (empty($page)) {
			throw new NotFoundException();
		}
		$this->set('page', $page);
	}

/**
 * セッティングモード切り替えアクション
 *
 * @return void
 */
	public function change_setting_mode() {
		if (isset($this->request->query['mode'])) {
			$settingMode = (bool)$this->request->query['mode'];
		} else {
			$settingMode = false;
		}
		$isSettingMode = Current::isSettingMode($settingMode);
		if ($isSettingMode) {
			$redirectUrl = NetCommonsUrl::backToPageUrl(true);
		} else {
			$redirectUrl = NetCommonsUrl::backToPageUrl();
		}
		$this->redirect($redirectUrl);
	}

}
