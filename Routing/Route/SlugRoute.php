<?php
/**
 * SlugRoute
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Page', 'Pages.Model');

/**
 * Automatically slugs routes based on named parameters
 *
 */
class SlugRoute extends CakeRoute {

/**
 * 何度も同じRouteチェックをさせないために、一度チェックしたものは、キャッシュする
 *
 * @var array
 */
	private $__executedRoute = [];

/**
 * 何度も同じRouteチェックをさせないために、一度チェックしたものは、キャッシュする
 *
 * @var array
 */
	private $__defaultSpace = null;

/**
 * parse
 *
 * @param string $url The URL to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 *
 * 速度改善の修正に伴って発生したため抑制
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
	public function parse($url) {
		$params = parent::parse($url);

		if (empty($params)) {
			return false;
		}

		$PageModel = ClassRegistry::init('Pages.Page');
		$dataSource = ConnectionManager::getDataSource($PageModel->useDbConfig);
		$tables = $dataSource->listSources();
		if (! in_array($PageModel->tablePrefix . $PageModel->useTable, $tables)) {
			return false;
		}

		$passKey = json_encode($params['pass']);
		if (array_key_exists($passKey, $this->__executedRoute)) {
			return $this->__executedRoute[$passKey];
		}

		$this->Space = ClassRegistry::init('Rooms.Space');
		if ($params['pass']) {
			$spaces = $this->Space->getSpaces();
			$result = [];
			foreach ($spaces as $row) {
				if ($row['Space']['permalink'] == $params['pass'][0]) {
					$result = $row;
					break;
				}
			}
			if ($result) {
				$params['spacePermalink'] = $result['Space']['permalink'];
				$params['spaceId'] = $result['Space']['id'];
				unset($params['pass'][0]);
			}
			$params['pass'] = array_values($params['pass']);
		}
		if (! isset($params['spaceId'])) {
			$result = $this->__findDefaultSpace();
			if (isset($result['Space'])) {
				$params['spacePermalink'] = $result['Space']['permalink'];
				$params['spaceId'] = $result['Space']['id'];
			} else {
				$params['spaceId'] = null;
				$params['spacePermalink'] = null;
			}
		}

		$path = implode('/', $params['pass']);
		if ($path === '') {
			$conditions = array('Page.sort_key' => Page::HOME_SORT_KEY);
		} else {
			$conditions = array(
				'Page.permalink' => $path,
				'Room.space_id' => $params['spaceId']
			);
		}

		$Room = ClassRegistry::init('Rooms.Room');
		$count = $PageModel->find('count', array(
			'conditions' => $conditions,
			'recursive' => -1,
			'joins' => [
				[
					'table' => $Room->table,
					'alias' => $Room->alias,
					'type' => 'INNER',
					'conditions' => array(
						$Room->alias . '.id' . ' = ' . $PageModel->alias . '.room_id',
					),
				]
			],
		));

		if ($count) {
			$params['pagePermalink'] = $params['pass'];
			$this->__executedRoute[$passKey] = true;
			return $params;
		}

		$this->__executedRoute[$passKey] = false;
		return false;
	}

/**
 * デフォルトのスペースデータ取得
 *
 * @return array
 */
	private function __findDefaultSpace() {
		if ($this->__defaultSpace) {
			return $this->__defaultSpace;
		} else {
			$result = $this->Space->cacheFindQuery('first', array(
				'fields' => ['id', 'permalink'],
				'conditions' => array('permalink' => '', 'id !=' => Space::WHOLE_SITE_ID),
				'recursive' => -1
			));
			$this->__defaultSpace = $result;
		}

		return $result;
	}

}
