<?php
/**
 * ページ表示 Controller
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @author Wataru Nishimoto <watura@willbooster.com>
 * @author Kazunori Sakamoto <exkazuu@willbooster.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('PagesAppController', 'Pages.Controller');
App::uses('Space', 'Rooms.Model');
App::uses('NetCommonsUrl', 'NetCommons.Utility');
App::uses('CurrentLibPage', 'NetCommons.Lib/Current');
App::uses('NetCommonsCDNCache', 'NetCommons.Utility');

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
		$this->Auth->allow('clear');
		//CurrentPage::__getPageConditionsでページ表示として扱う
		if ($this->params['action'] === 'index') {
			$this->request->params['pageView'] = true;
		}
		parent::beforeFilter();

		$this->CurrentLibPage = CurrentLibPage::getInstance();
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
			Current::setSettingMode(false);
			return $this->redirect('/' . $path);
		}

		//ページのパーマリンクにjpgやjsonなどを設定した場合に、
		//各Viewが読み込まれてしまうため、当アクションに入ってきた場合は、強制的にhtmlとして出力する
		$this->viewPath = 'Pages';
		$this->layoutPath = null;
		$this->response->type('html');

		//ページデータの取得
		$paths = $this->params->params['pass'];
		$path = implode('/', $paths);

		$spacePermalink = Hash::get($this->request->params, 'spacePermalink', '');
		$space = $this->Space->cacheFindQuery('first', array(
			'recursive' => -1,
			'conditions' => array('permalink' => $spacePermalink, 'id !=' => Space::WHOLE_SITE_ID)
		));

		$pageId = $this->CurrentLibPage->getPageIdByPermalink($path, $space['Space']['id']);
		if (empty($pageId)) {
			throw new NotFoundException();
		}
		$page = $this->CurrentLibPage->findCurrentPageWithContainer();
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
		Current::setSettingMode($settingMode);
		$isSettingMode = Current::isSettingMode();
		if ($isSettingMode) {
			$redirectUrl = NetCommonsUrl::backToPageUrl(true);
		} else {
			$redirectUrl = NetCommonsUrl::backToPageUrl();
		}
		$this->redirect($redirectUrl);
	}

/**
 * CDN Cache を削除する
 *
 * @return void
 */
	public function clear() {
		$cdnCache = new NetCommonsCDNCache();
		$cdnCache->invalidate();
		$this->redirect('/');
	}

}
