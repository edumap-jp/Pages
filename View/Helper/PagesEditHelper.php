<?php
/**
 * LayoutHelper
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Page', 'Pages.Model');
App::uses('Folder', 'Utility');

/**
 * LayoutHelper
 *
 */
class PagesEditHelper extends AppHelper {

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
	);

/**
 * ページ順序配列
 *
 * @var array
 */
	protected $_pageWeight = array();

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->css('/pages/css/style.css');
		parent::beforeRender($viewFile);
	}

/**
 * レイアウト変更のimgデータ取得
 *
 * @return array imgリスト
 */
	public function getLayouts() {
		$dir = new Folder(
			CakePlugin::path('Pages') . WEBROOT_DIR . DS . 'img' . DS . 'layouts'
		);
		$files = $dir->find('.*\.png', true);

		$usageLimit = ! SiteSettingUtil::read('App.display_ads');
		foreach ($files as $i => $file) {
			if (! $usageLimit &&
					preg_match('/^[0-1]_0_0_[0-1]\.png$/', $file)) {
				unset($files[$i]);
			}
		}

		return $files;
	}

/**
 * インデント
 *
 * @param int $pageId ページID
 * @return string HTML
 */
	public function indent($pageId) {
		$parentId = $this->_View->viewVars['pages'][$pageId]['Page']['parent_id'];
		$nest = $this->_View->viewVars['parentList']['_' . $parentId]['_' . $pageId]['nest'];
		return str_repeat('<span class="pages-tree"> </span> ', $nest);
	}

/**
 * タイトルタグのヘルプの表示
 *
 * @return string ヘルプHTML出力
 */
	public function helpMetaTitle() {
		$html = '';

		$html .= __d('pages',
				'The keywords in the title influences the search order of the search engine. ' .
				'The keyword in the head is important.') . '<br>';

		$content = __d('net_commons', '{X-SITE_NAME} : Site name') . '<br>';
		$content .= __d('pages', '{X-PAGE_NAME} : Page name');

		$content = __d('net_commons', 'Each of the embedded keywords, will be converted ' .
				'to the corresponding content. <br />') . $content;

		$html .= __d('pages', 'The item can use an embedded keyword.') . ' ';

		$html .= '<a href="" data-toggle="popover" data-placement="bottom"' .
					' title="' . __d('mails', 'Embedded keyword?') . '"' . ' data-content="' . $content . '">';
		$html .= '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
		$html .= '</a>';
		$html .= '<script type="text/javascript">' .
			'$(function () { $(\'[data-toggle="popover"]\').popover({html: true}) });</script>';

		return $html;
	}

}
