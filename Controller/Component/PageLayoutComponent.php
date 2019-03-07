<?php
/**
 * ページLayout Component
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Component', 'Controller');
App::uses('CurrentLibPage', 'NetCommons.Lib/Current');

/**
 * ページLayout Component
 *
 * @package NetCommons\Pages\Controller\Component
 */
class PageLayoutComponent extends Component {

/**
 * modalフラグ
 *
 * @var string
 */
	public $modal = null;

/**
 * page
 *
 * @var string
 */
	protected static $_page = null;

/**
 * フレームエレメント
 *
 * @var string
 */
	public $frameElement = 'Frames.frame';

/**
 * beforeRender
 *
 * @param Controller $controller Controller
 * @return void
 * @throws NotFoundException
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
	public function beforeRender(Controller $controller) {
		// Ajax用
		if ($controller->request->is('ajax')) {
			return;
		}

		if (!self::$_page) {
			//pathからページデータ取得
			if (! isset($controller->viewVars['page'])) {
				self::$_page = CurrentLibPage::getInstance()->findCurrentPageWithContainer();
			} else {
				self::$_page = $controller->viewVars['page'];
			}
		}

		if (! array_key_exists('Pages.PageLayout', $controller->helpers)) {
			$controller->helpers['Pages.PageLayout'] = array(
				'page' => self::$_page,
				'layoutSetting' => ($controller->layout === 'NetCommons.setting'),
				'frameElement' => $this->frameElement,
			);
		}

		//RequestActionの場合、スキップする
		if (! empty($controller->request->params['requested'])) {
			return;
		}

		$controller->set('page', self::$_page);
		$controller->set('modal', $this->modal);

		//メタデータのセット
		$this->setMeta($controller);

		//ヘルパーセット
		if (! array_key_exists('NetCommons.Composer', $controller->helpers)) {
			$controller->helpers[] = 'NetCommons.Composer';
		}

		//表示言語フレームのセット
		if ($controller->layout === 'NetCommons.setting' && Current::read('Frame.id')) {
			$this->setFramePublicLang($controller);
		}

		//Layoutのセット
		if (! $controller->layout ||
				in_array($controller->layout, ['NetCommons.default', 'NetCommons.setting'], true)) {
			$controller->layout = 'Pages.default';
		}
	}

/**
 * メタデータのviewVarsセット処理
 *
 * @param Controller $controller Controller
 * @return void
 */
	public function setMeta(Controller $controller) {
		App::uses('PagesLanguage', 'Pages.Model');

		//メタデータのセット
		$meta['title'] = Hash::get(
			$controller->viewVars['page'],
			'PagesLanguage.meta_title',
			PagesLanguage::DEFAULT_META_TITLE
		);
		$meta['description'] = Hash::get(
			$controller->viewVars['page'],
			'PagesLanguage.meta_description',
			SiteSettingUtil::read('Meta.description')
		);
		$meta['keywords'] = Hash::get(
			$controller->viewVars['page'],
			'PagesLanguage.meta_keywords',
			SiteSettingUtil::read('Meta.keywords')
		);
		$meta['robots'] = Hash::get(
			$controller->viewVars['page'],
			'PagesLanguage.meta_robots',
			SiteSettingUtil::read('Meta.robots')
		);
		$meta['copyright'] = SiteSettingUtil::read('Meta.copyright');
		$meta['author'] = SiteSettingUtil::read('Meta.author');

		$result = array();
		foreach ($meta as $key => $value) {
			$value = str_replace(
				'{X-SITE_NAME}', SiteSettingUtil::read('App.site_name'), $value
			);
			$value = str_replace(
				'{X-PAGE_NAME}', Hash::get($controller->viewVars['page'], 'PagesLanguage.name'), $value
			);

			if ($key === 'title') {
				$controller->set('pageTitle', $value);
			} else {
				$result[] = array('name' => $key, 'content' => $value);
			}
		}

		$controller->set('meta', $result);
	}

/**
 * フレームの表示言語設定に関するデータをviewVarsセットする
 *
 * @param Controller $controller Controller
 * @return void
 */
	public function setFramePublicLang(Controller $controller) {
		$this->Language = ClassRegistry::init('M17n.Language');
		$this->FramePublicLanguage = ClassRegistry::init('Frames.FramePublicLanguage');

		$activeLangs = $this->Language->getLanguages();
		list(, $enableLangs) = $this->Language->getLanguagesWithName();

		$framePublicLangs = $this->FramePublicLanguage->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'language_id'),
			'conditions' => array(
				'frame_id' => Current::read('Frame.id'),
				'is_public' => true
			),
		));

		$frameLangs = array();
		$publicLangs = array();
		foreach ($activeLangs as $language) {
			$langId = (int)$language['Language']['id'];
			$langCode = $language['Language']['code'];

			$frameLangs[$langId] = __d(
				'frames', 'Display of the %s page', Hash::get($enableLangs, $langCode)
			);

			if (in_array((string)$langId, $framePublicLangs, true) ||
					in_array('0', $framePublicLangs, true)) {
				$publicLangs[$langId] = $langId;
			}
		}
		$controller->set('framePublicLangs', $publicLangs);
		$controller->set('frameLangs', $frameLangs);
	}

}
