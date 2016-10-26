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
App::uses('Container', 'Containers.Model');

/**
 * LayoutHelper
 *
 */
class PageLayoutHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'NetCommons.NetCommonsHtml',
	);

/**
 * Bootstrap col max size
 *
 * @var int
 */
	const COL_MAX_SIZE = 12;

/**
 * Bootstrap col-sm default size
 *
 * @var int
 */
	const COL_DEFAULT_SIZE = 3;

/**
 * Containers data
 *
 * @var array
 */
	public $containers;

///**
// * Boxes data
// *
// * @var array
// */
//	public $boxes;

/**
 * Plugins data
 *
 * @var array
 */
	public $plugins;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);

		$this->containers = Hash::combine(
			Hash::get($settings, 'page.PageContainer', array()), '{n}.container_type', '{n}'
		);

//		$this->boxes = Hash::combine(
//			Hash::get($settings, 'page.Box', array()), '{n}.id', '{n}', '{n}.container_id'
//		);
		$this->plugins = Hash::combine(Current::read(
			'PluginsRoom', array()), '{n}.Plugin.key', '{n}.Plugin'
		);
	}

/**
 * マジックメソッド。
 *
 * @param string $method メソッド
 * @param array $params パラメータ
 * @return string
 */
	public function __call($method, $params) {
		if ($method === 'getBlockStatus') {
			$helper = $this->_View->loadHelper('Blocks.Blocks');
		}
		return call_user_func_array(array($helper, $method), $params);
	}

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

		//メタデータ
		$metas = Hash::get($this->_View->viewVars, 'meta', array());
		foreach ($metas as $meta) {
			$this->NetCommonsHtml->meta($meta, null, ['inline' => false]);
		}

		parent::beforeRender($viewFile);
	}

/**
 * Before layout callback. beforeLayout is called before the layout is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $layoutFile The layout about to be rendered.
 * @return void
 */
	public function beforeLayout($layoutFile) {
		$this->_View->viewVars['pageHeader'] = $this->_View->element('Pages.page_header');
		$this->_View->viewVars['pageMajor'] = $this->_View->element('Pages.page_major');
		$this->_View->viewVars['pageMinor'] = $this->_View->element('Pages.page_minor');
		$this->_View->viewVars['pageFooter'] = $this->_View->element('Pages.page_footer');

		parent::beforeLayout($layoutFile);
	}

/**
 * After render callback. afterRender is called after the view file is rendered
 * but before the layout has been rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that was rendered.
 * @return void
 */
	public function afterRender($viewFile) {
		$attributes = array(
			'id' => 'container-main',
			'role' => 'main'
		);

		if ($this->_View->layout === 'NetCommons.setting') {
			//Frame設定も含めたコンテンツElement
			$element = $this->_View->element('Frames.setting_frame', array(
				'view' => $this->_View->fetch('content')
			));

			//属性
			$attributes['ng-controller'] = 'FrameSettingsController';

			$frameCamelize = NetCommonsAppController::camelizeKeyRecursive(Current::read('Frame'));
			$attributes['ng-init'] = 'initialize({frame: ' . json_encode($frameCamelize) . '})';

			//セッティングモード
			$this->_View->viewVars['isSettingMode'] = true;

		} else {
			//コンテンツElement
			if ($this->_View->request->params['plugin'] === 'pages') {
				$element = $this->_View->fetch('content');
			} else {
				$element = $this->_View->element('Frames.frame', array(
					'frame' => Current::read('Frame'),
					'view' => $this->_View->fetch('content'),
					'centerContent' => true,
					'displayBackTo' => Hash::get($this->_View->viewVars, 'displayBackTo', false)
				));
			}
			//セッティングモード
			$this->_View->viewVars['isSettingMode'] = Current::isSettingMode();
		}

		//ページコンテンツのセット
		$this->_View->viewVars['pageContent'] = $this->Html->div(
			array($this->containerSize(Container::TYPE_MAIN)), $element, $attributes
		);

		//Layoutのセット
		$this->_View->layout = 'Pages.default';

		//
		if (Current::read('Page.is_container_fluid')) {
			$this->_View->viewVars['pageContainerCss'] = 'container-fluid';
		} else {
			$this->_View->viewVars['pageContainerCss'] = 'container';
		}
	}

/**
 * Get the container size for layout
 *
 * @param string $containerType Container type.
 *    e.g.) Container::TYPE_MAJOR or Container::TYPE_MAIN or Container::TYPE_MINOR
 * @return string Html class attribute
 */
	public function containerSize($containerType) {
		$result = '';

		$mainCol = self::COL_MAX_SIZE;
		if ($this->hasContainer(Container::TYPE_MAJOR)) {
			$mainCol -= self::COL_DEFAULT_SIZE;
		}
		if ($this->hasContainer(Container::TYPE_MINOR)) {
			$mainCol -= self::COL_DEFAULT_SIZE;
		}

		switch ($containerType) {
			case Container::TYPE_MAJOR:
				if ($this->hasContainer($containerType)) {
					$result = ' col-md-' . self::COL_DEFAULT_SIZE . ' col-md-pull-' . $mainCol;
				}
				break;
			case Container::TYPE_MINOR:
				if ($this->hasContainer($containerType)) {
					$result = ' col-md-' . self::COL_DEFAULT_SIZE;
				}
				break;
			default:
				$result = ' col-md-' . $mainCol;
				if ($this->hasContainer(Container::TYPE_MAJOR)) {
					$result .= ' col-md-push-' . self::COL_DEFAULT_SIZE;
				}
		}

		return trim($result);
	}

/**
 * レイアウトの有無チェック
 *
 * @param string $containerType コンテナータイプ
 *    e.g.) Container::TYPE_HEADER or TYPE_MAJOR or TYPE_MAIN or TYPE_MINOR or TYPE_FOOTER
 * @param bool $layoutSetting レイアウト変更画面かどうか
 * @return bool The layout have container
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function hasContainer($containerType, $layoutSetting = false) {
		$result = Hash::get($this->containers, $containerType . '.is_published', false);
		if (! $result) {
			return false;
		}

		if (! Current::isSettingMode() && ! $layoutSetting) {
			$box = $this->getBox($containerType);
			$frames = Hash::combine($box, '{n}.Frame.{n}.id', '{n}.Frame.{n}');
			$result = count($frames);
		}

		return (bool)$result;
	}

/**
 * Get the box data for container
 *
 * @param string $containerType Container type.
 *    e.g.) Container::TYPE_MAJOR or Container::TYPE_MAIN or Container::TYPE_MINOR
 * @return array Box data
 */
	public function getBox($containerType) {
		return Hash::get($this->containers, $containerType . '.Box', array());
	}

}
