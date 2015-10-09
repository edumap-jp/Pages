<?php
/**
 * SlugRoute
 */

App::uses('ClassRegistry', 'Utility');

/**
 * Automatically slugs routes based on named parameters
 *
 */
class SlugRoute extends CakeRoute {

/**
 * parse
 *
 * @param string $url The URL to attempt to parse.
 * @return mixed Boolean false on failure, otherwise an array or parameters
 */
	public function parse($url) {
		$params = parent::parse($url);

		if (empty($params)) {
			return false;
		}

		$Page = ClassRegistry::init('Pages.Page');
		$dataSource = ConnectionManager::getDataSource($Page->useDbConfig);
		$tables = $dataSource->listSources();
		if (!in_array($Page->useTable, $tables)) {
			return false;
		}

		$path = implode('/', $params['pass']);
		$count = $Page->find('count', array(
			'conditions' => array('Page.permalink' => $path),
			'recursive' => -1
		));

		if ($count) {
			return $params;
		}

		return false;
	}

}