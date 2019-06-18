<?php
/**
 * Page Behavior
 *
 * @property Room $Room
 * @property Page $ParentPage
 * @property Box $Box
 * @property Page $ChildPage
 * @property Box $Box
 * @property Container $Container
 * @property Language $Language
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('ModelBehavior', 'Model');
App::uses('Space', 'Rooms.Model');

/**
 * Page Behavior
 *
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @package NetCommons\Pages\Model
 */
class GetPageBehavior extends ModelBehavior {

/**
 * 何度も同じ条件で取得しないようにキャッシュする
 *
 * @var array
 */
	private static $__memoryPages = [];

/**
 * 何度も同じ条件で取得しないようにキャッシュする
 *
 * @var array
 */
	private static $__memoryPageWithFrame = [];

/**
 * ページデータ取得
 *
 * @param Model $model Model using this behavior
 * @param int|array $roomIds Room.id
 * @return array
 */
	public function getPages(Model $model, $roomIds = null) {
		$model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
		]);

		if (! isset($roomIds)) {
			$roomIds = Current::read('Room.id');
		}

		//同じ条件で一度取得していれば、キャッシュのデータを戻す
		$cacheId = json_encode($roomIds);
		if (isset(self::$__memoryPages[$cacheId])) {
			return self::$__memoryPages[$cacheId];
		}

		$pagesLanguages = $model->PagesLanguage->find('all', array(
			'fields' => array(
				'PagesLanguage.page_id',
				'PagesLanguage.language_id',
				'PagesLanguage.name',
			),
			'recursive' => 0,
			'conditions' => $model->PagesLanguage->getConditions(array(
				'Page.room_id' => $roomIds,
			)),
		));
		$retPagesLanguages = [];

		foreach ($pagesLanguages as $pageLanguage) {
			$retPagesLanguages[$pageLanguage['PagesLanguage']['page_id']] = $pageLanguage;
		}

		$model->unbindModel(array('hasMany' => array('PageContainer')));
		$model->unbindModel(array('belongsTo' => array('PagesLanguage', 'OriginPagesLanguage')));
		$pages = $model->find('all', array(
			'fields' => array(
				'Page.id', 'Page.room_id', 'Page.root_id',
				'Page.parent_id',
				//'Page.lft', 'Page.rght',
				'Page.weight', 'Page.sort_key', 'Page.child_count',
				'Page.permalink', 'Page.slug',
				// 'Page.is_container_fluid', 'Page.theme',
				'Room.id',
				'Room.space_id',
				'Room.page_id_top',
				'Room.parent_id',
				//'Room.lft',
				//'Room.rght',
				'Room.active',
				'Room.in_draft',
				'Room.default_role_key',
				//'Room.need_approval',
				//'Room.default_participation',
				//'Room.page_layout_permitted',
				//'Room.theme',
				'Space.id', 'Space.permalink',
			),
			'recursive' => 1,
			'conditions' => array(
				'Page.id' => array_keys($retPagesLanguages),
			),
		));

		$result = [];
		foreach ($pages as $page) {
			$result[$page['Page']['id']] = $page;
			$result[$page['Page']['id']]['PagesLanguage'] =
							$retPagesLanguages[$page['Page']['id']]['PagesLanguage'];
		}

		if ($model->useDbConfig !== 'test') {
			self::$__memoryPages[$cacheId] = $result;
		}
		return $result;
	}

/**
 * ページデータ取得
 *
 * @param Model $model Model using this behavior
 * @param int|array $pageIds ページID
 * @return array
 */
	public function getPageIdsWithM17n(Model $model, $pageIds) {
		$model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
		]);

		$pagesLanguages = $model->PagesLanguage->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'language_id', 'page_id'),
			'conditions' => array(
				'page_id' => $pageIds,
			),
		));

		return $pagesLanguages;
	}

/**
 * Frameデータも一緒にページデータ取得
 *
 * @param Model $model Model using this behavior
 * @param string $permalink Permalink
 * @param string $spaceId Space id
 * @return array
 */
	public function getPageWithFrame(Model $model, $permalink, $spaceId = null) {
		$model->loadModels([
			'Box' => 'Boxes.Box',
			'PagesLanguage' => 'Pages.PagesLanguage',
			'PageContainer' => 'Pages.PageContainer',
			'Space' => 'Rooms.Space',
		]);

		// permalinkがnullの場合、nullで検索をかけてしまうため
		if (is_null($permalink)) {
			return array('PageContainer' => array());
		}

		// トップページの場合空にする
		if ($permalink == Current::read('TopPage.permalink')) {
			$permalink = '';
		}

		if ($permalink === '') {
			$conditions = array(
				'Page.id' => Current::read('TopPage.id')
			);
		} else {
			$conditions = array(
				'Page.permalink' => $permalink,
				'Room.space_id' => $spaceId
			);
		}

		//同じ条件で一度取得していれば、キャッシュのデータを戻す
		$cacheId = json_encode($conditions);
		if (isset(self::$__memoryPageWithFrame[$cacheId])) {
			return self::$__memoryPageWithFrame[$cacheId];
		}

		if (isset($model->belongsTo['Room'])) {
			$model->bindModel(array(
				'belongsTo' => array(
					'Space' => array(
						'className' => 'Rooms.Space',
						'foreignKey' => false,
						'conditions' => array(
							'Room.space_id = Space.id',
						),
						'fields' => '',
						'order' => ''
					),
				)
			), true);
			$model->Room->useDbConfig = $model->useDbConfig;
			$model->Space->useDbConfig = $model->useDbConfig;
		}

		$query = array(
			'recursive' => 0,
			'conditions' => $conditions,
		);
		$page = $model->find('first', $query);

		$pageId = Hash::get($page, 'Page.id');
		$pagesLanguages = $model->PagesLanguage->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'PagesLanguage.page_id' => $pageId,
				'PagesLanguage.language_id' => Current::read('Language.id'),
			),
		));
		$result = Hash::merge($page, $pagesLanguages);

		$pageContainers = $model->PageContainer->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'PageContainer.page_id' => $pageId,
			),
			'order' => array('container_type' => 'asc'),
		));
		$result['PageContainer'] = Hash::extract($pageContainers, '{n}.PageContainer');
		foreach ($result['PageContainer'] as $i => $pageContainer) {
			$pageContainer['Box'] = $model->Box->getBoxWithFrame($pageContainer['id']);
			$result['PageContainer'][$i] = $pageContainer;
		}

		if ($model->useDbConfig !== 'test') {
			self::$__memoryPageWithFrame[$cacheId] = $result;
		}

		return $result;
	}

/**
 * トップページの取得
 *
 * @param Model $model Model using this behavior
 * @return int ページID
 */
	public function getTopPageId(Model $model) {
		$model->loadModels([
			'Room' => 'Rooms.Room',
		]);

		$room = $model->Room->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID)
			)
		));

		return Hash::get($room, 'Room.page_id_top');
	}

/**
 * 親ノード名を取得
 *
 * @param Model $model Model using this behavior
 * @param int $pageId ページID
 * @return array 親ノード名リスト
 */
	public function getParentNodeName(Model $model, $pageId) {
		$model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
		]);

		$parentNode = $model->getPath($pageId);

		$pagesLanguages = $model->find('list', array(
			'recursive' => -1,
			'fields' => array(
				$model->PagesLanguage->alias . '.page_id',
				$model->PagesLanguage->alias . '.name',
			),
			'conditions' => array(
				$model->alias . '.id' => Hash::extract($parentNode, '{n}.Page.id'),
				$model->alias . '.parent_id NOT' => null,
			),
			'joins' => array(
				array(
					'table' => $model->PagesLanguage->table,
					'alias' => $model->PagesLanguage->alias,
					'conditions' => array(
						$model->PagesLanguage->alias . '.page_id' . ' = ' . $model->alias . '.id',
						$model->PagesLanguage->alias . '.language_id' => Current::read('Language.id'),
					),
				),
			),
			'order' => array($model->alias . ' .sort_key' => 'asc')
		));

		return $pagesLanguages;
	}

/**
 * 親ページのPage.permalinkを取得
 *
 * @param Model $model Model using this behavior
 * @param array $page Page data
 * @return string ルーム内先頭のPage.permalink
 */
	public function getParentPermalink(Model $model, $page) {
		// ・パブリックのトップIDの空返却は、ページ下ページで空返却してしまうため、削る
		// ・Room.page_id_topとpage_idが同じで空返却は、サブルームも空返却してしまうため、削る
		//if ($page['room_id'] === Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID) ||
		//	Hash::get($page, ['id'], false) === Current::read('Room.page_id_top')
		//) {
		//	return '';
		//}

		$topPageOfRoom = $model->findById(
			// 親ページIDのpermalinkを取得
			//Current::read('Room.page_id_top'),
			Hash::get($page, ['parent_id'], false),
			'permalink',
			null,
			-1
		);
		if (!$topPageOfRoom) {
			return '';
		}

		return $topPageOfRoom['Page']['permalink'];
	}

}
