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
App::uses('Box', 'Boxes.Model');
App::uses('Current', 'NetCommons.Utility');

/**
 * LayoutHelper
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PageLayoutHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'NetCommons.Button',
		'NetCommons.NetCommonsForm',
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
 * コンテナー変数
 *
 * 何度も同じ処理をさせないために保持する
 *
 * @var array
 */
	protected static $_containers;

/**
 * プラグインデータ
 *
 * 何度も同じ処理をさせないために保持する
 *
 * @var array
 */
	protected static $_plugins;

/**
 * Containers data
 *
 * @var array
 */
	public $containers;

/**
 * Plugins data
 *
 * @var array
 */
	public $plugins;

/**
 * LayoutがNetCommons.settingかどうか
 *
 * @var array
 */
	public $layoutSetting;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		$settings += [
			'routingFormat' => ['/:plugin/:controller/:action'],
		];

		parent::__construct($View, $settings);

		$isTestMock = (substr(get_class($this->_View->request), 0, 4) === 'Mock');

		if (! self::$_containers || $isTestMock) {
			if (isset($settings['page']['PageContainer'])) {
				foreach ($settings['page']['PageContainer'] as $container) {
					self::$_containers[$container['container_type']] = $container;
				}
			} else {
				self::$_containers = [];
			}
		}
		$this->containers = self::$_containers;

		if (! self::$_plugins || $isTestMock) {
			self::$_plugins = [];
			$pluginRooms = Current::read('PluginsRoom', array());
			foreach ($pluginRooms as $plugin) {
				self::$_plugins[$plugin['Plugin']['key']] = $plugin['Plugin'];
			}
		}
		$this->plugins = self::$_plugins;

		$this->layoutSetting = Hash::get($settings, 'layoutSetting', false);
	}

/**
 * マジックメソッド。
 *
 * @param string $method メソッド
 * @param array $params パラメータ
 * @return string
 */
	public function __call($method, $params) {
		$boxMethods = array(
			'getBox', 'boxTitle', 'displayBoxSetting', 'hasBox',
			'hasBoxSetting', 'renderAddPlugin', 'renderFrames', 'renderBoxes',
		);
		$frameMethods = array(
			'frameActionUrl', 'frameSettingLink', 'frameSettingQuitLink',
			'frameOrderButton', 'frameDeleteButton',
		);

		if ($method === 'getBlockStatus') {
			$helper = $this->_View->loadHelper('Blocks.Blocks');
			return call_user_func_array(array($helper, $method), $params);

		} elseif (in_array($method, $boxMethods, true)) {
			$helper = $this->_View->loadHelper(
				'Boxes.Boxes', array('containers' => $this->containers)
			);
			return call_user_func_array(array($helper, $method), $params);

		} elseif (in_array($method, $frameMethods, true)) {
			$helper = $this->_View->loadHelper(
				'Frames.Frames', array('plugins' => $this->plugins)
			);
			return call_user_func_array(array($helper, $method), $params);
		}
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
		$this->NetCommonsHtml->css('/boxes/css/style.css');
		$this->NetCommonsHtml->script('/boxes/js/boxes.js');

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
		if ($this->hasContainer(Container::TYPE_HEADER)) {
			$this->_View->viewVars['pageHeader'] = $this->_View->element('Pages.page_header');
		} else {
			$this->_View->viewVars['pageHeader'] = '';
		}
		if ($this->hasContainer(Container::TYPE_MAJOR)) {
			$this->_View->viewVars['pageMajor'] = $this->_View->element('Pages.page_major');
		} else {
			$this->_View->viewVars['pageMajor'] = '';
		}
		if ($this->hasContainer(Container::TYPE_MINOR)) {
			$this->_View->viewVars['pageMinor'] = $this->_View->element('Pages.page_minor');
		} else {
			$this->_View->viewVars['pageMinor'] = '';
		}
		if ($this->hasContainer(Container::TYPE_FOOTER)) {
			$this->_View->viewVars['pageFooter'] = $this->_View->element('Pages.page_footer');
		} else {
			$this->_View->viewVars['pageFooter'] = '';
		}

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

		if ($this->layoutSetting && Current::read('Frame')) {
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
				$frame = Hash::merge(
					Current::read('FramesLanguage', array(
						'name' => Current::read('Plugin.name')
					)),
					Current::read('Frame', array(
						'header_type' => null,
						'id' => null,
						'plugin_key' => Current::read('Plugin.key'),
					))
				);

				if (isset($this->settings['frameElement'])) {
					$frameElement = $this->settings['frameElement'];
				} else {
					$frameElement = 'Frames.frame';
				}
				$element = $this->_View->element($frameElement, array(
					'frame' => $frame,
					'view' => $this->_View->fetch('content'),
					'centerContent' => true,
					'containerType' => Current::read('BoxesPageContainer.container_type'),
					'box' => array(
						'Box' => Current::read('Box'),
						'BoxesPageContainer' => Current::read('BoxesPageContainer'),
					),
				));
			}
			//セッティングモード
			$this->_View->viewVars['isSettingMode'] = Current::isSettingMode();
		}

		//ページコンテンツのセット
		$this->_View->viewVars['pageContent'] = $this->_View->element('Pages.page_main', array(
			'element' => $element,
			'attributes' => $attributes
		));

		if (Current::read('Page.is_container_fluid')) {
			$this->_View->viewVars['pageContainerCss'] = 'container-fluid';
		} else {
			$this->_View->viewVars['pageContainerCss'] = 'container';
		}
	}

/**
 * Get the container size for layout
 *
 * @param string $containerType コンテナータイプ
 *		Container::TYPE_HEADER or Container::TYPE_MAJOR or Container::TYPE_MAIN or
 *		Container::TYPE_MINOR or Container::TYPE_FOOTER
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
 *		Container::TYPE_HEADER or Container::TYPE_MAJOR or Container::TYPE_MAIN or
 *		Container::TYPE_MINOR or Container::TYPE_FOOTER
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
 * requestAction()のオーバライド
 *
 * $urlは、/:plugin/:controller/:action?xxxx=yyyy&xxxx2=yyyy2 形式とする。
 *
 * @param string $url $url String or array-based URL. Unlike other URL arrays in CakePHP, this
 *    URL will not automatically handle passed and named arguments in the $url parameter.
 * @param array $extra if array includes the key "return" it sets the AutoRender to true. Can
 *    also be used to submit GET/POST data, and named/passed arguments.
 * @return bool true or false on success/failure, or contents
 *    of rendered action if 'return' is set in $extra.
 */
	public function requestAction($url, $extra = array()) {
		if (empty($url)) {
			return false;
		}

		$params = $this->_parseParams($url);
		if (! $params) {
			return parent::requestAction($url, $extra);
		}

		if (($index = array_search('return', $extra)) !== false) {
			$extra['return'] = 0;
			$extra['autoRender'] = 1;
			unset($extra[$index]);
		}

		$extra += array('autoRender' => 0, 'return' => 1, 'bare' => 1, 'requested' => 1);
		$data = isset($extra['data']) ? $extra['data'] : null;
		unset($extra['data']);

		$request = new CakeRequest($url);

		if (isset($data)) {
			$request->data = $data;
		}

		$this->_setParams($request, $params, $extra);
		Router::setRequestInfo($request);
		$result = $this->_dispatch($request, new CakeResponse(), $extra);
		Router::popRequest();
		return $result;
	}

/**
 * Dispatches and invokes given Request, handing over control to the involved controller. If the controller is set
 * to autoRender, via Controller::$autoRender, then Dispatcher will render the view.
 *
 * Actions in CakePHP can be any public method on a controller, that is not declared in Controller. If you
 * want controller methods to be public and in-accessible by URL, then prefix them with a `_`.
 * For example `public function _loadPosts() { }` would not be accessible via URL. Private and protected methods
 * are also not accessible via URL.
 *
 * If no controller of given name can be found, invoke() will throw an exception.
 * If the controller is found, and the action is not found an exception will be thrown.
 *
 * @param CakeRequest $request Request object to dispatch.
 * @param CakeResponse $response Response object to put the results of the dispatch into.
 * @return string|null if `$request['return']` is set then it returns response body, null otherwise
 * @throws MissingControllerException When the controller is missing.
 */
	protected function _dispatch(CakeRequest $request, CakeResponse $response) {
		$controller = $this->_getController($request, $response);

		if (!($controller instanceof Controller)) {
			throw new MissingControllerException(array(
				'class' => Inflector::camelize($request->params['controller']) . 'Controller',
				'plugin' => empty($request->params['plugin']) ?
								null : Inflector::camelize($request->params['plugin'])
			));
		}

		$response = $this->_invoke($controller, $request);
		if (isset($request->params['return'])) {
			return $response->body();
		}
	}

/**
 * Applies Routing and additionalParameters to the request to be dispatched.
 * If Routes have not been loaded they will be loaded, and app/Config/routes.php will be run.
 *
 * @param CakeRequest $request Request object to dispatch.
 * @param array $params リクエストにセットするパラメータ
 * @param array $additionalParams Settings array ("bare", "return") which is melded with the GET and POST params
 * @return void
 */
	protected function _setParams(CakeRequest $request, $params, $additionalParams = []) {
		$request->addParams($params);
		if (!empty($additionalParams)) {
			$request->addParams($additionalParams);
		}
	}

/**
 * Applies Routing and additionalParameters to the request to be dispatched.
 * If Routes have not been loaded they will be loaded, and app/Config/routes.php will be run.
 *
 * @param string $url $url String or array-based URL. Unlike other URL arrays in CakePHP, this
 *    URL will not automatically handle passed and named arguments in the $url parameter.
 * @return array|false
 */
	protected function _parseParams($url) {
		if (strpos($url, '?') !== false) {
			list($url, $queryParameters) = explode('?', $url, 2);
			parse_str($queryParameters, $queryParameters);
		}
		if (substr($url, -1, 1) === '/') {
			$url = substr($url, 0, -1);
		}
		if (substr($url, 0, 1) !== '/') {
			$url = '/' . $url;
		}
		$urlArr = explode('/', $url);
		$countUrlArr = count($urlArr);

		$params = false;
		foreach ($this->settings['routingFormat'] as $format) {
			$formatArr = explode('/', $format);
			if ($countUrlArr === count($formatArr)) {
				for ($i = 1; $countUrlArr > $i; $i++) {
					$params[substr($formatArr[$i], 1)] = $urlArr[$i];
				}
				$params += ['named' => [], 'pass' => []];
				break;
			}
		}
		return $params;
	}

/**
 * Get controller to use, either plugin controller or application controller
 *
 * @param CakeRequest $request Request object
 * @param CakeResponse $response Response for the controller.
 * @return mixed name of controller if not loaded, or object if loaded
 */
	protected function _getController($request, $response) {
		$ctrlClass = $this->_loadController($request);
		if (!$ctrlClass) {
			return false;
		}
		$reflection = new ReflectionClass($ctrlClass);
		if ($reflection->isAbstract() || $reflection->isInterface()) {
			return false;
		}
		return $reflection->newInstance($request, $response);
	}

/**
 * Load controller and return controller class name
 *
 * @param CakeRequest $request Request instance.
 * @return string|bool Name of controller class name
 */
	protected function _loadController($request) {
		$pluginName = $pluginPath = $controller = null;
		if (!empty($request->params['plugin'])) {
			$pluginName = $controller = Inflector::camelize($request->params['plugin']);
			$pluginPath = $pluginName . '.';
		}
		if (!empty($request->params['controller'])) {
			$controller = Inflector::camelize($request->params['controller']);
		}
		if ($pluginPath . $controller) {
			$class = $controller . 'Controller';
			App::uses('AppController', 'Controller');
			App::uses($pluginName . 'AppController', $pluginPath . 'Controller');
			App::uses($class, $pluginPath . 'Controller');
			if (class_exists($class)) {
				return $class;
			}
		}
		return false;
	}

/**
 * Initializes the components and models a controller will be using.
 * Triggers the controller action, and invokes the rendering if Controller::$autoRender
 * is true and echo's the output. Otherwise the return value of the controller
 * action are returned.
 *
 * @param Controller $controller Controller to invoke
 * @param CakeRequest $request The request object to invoke the controller for.
 * @return CakeResponse the resulting response object
 */
	protected function _invoke(Controller $controller, CakeRequest $request) {
		$controller->constructClasses();
		$controller->startupProcess();

		$response = $controller->response;
		$render = true;
		$result = $controller->invokeAction($request);
		if ($result instanceof CakeResponse) {
			$render = false;
			$response = $result;
		}

		if ($render && $controller->autoRender) {
			$response = $controller->render();
		} elseif (!($result instanceof CakeResponse) && $response->body() === null) {
			$response->body($result);
		}
		$controller->shutdownProcess();

		return $response;
	}

}
