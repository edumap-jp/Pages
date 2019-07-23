<?php
/**
 * PageTree Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

//App::uses('TreeBehavior', 'Model/Behavior');
App::uses('NetCommonsTreeBehavior', 'NetCommons.Model/Behavior');

/**
 * Page Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Model\Behavior
 */
class PagesTreeBehavior extends NetCommonsTreeBehavior {

/**
 * A convenience method for returning a hierarchical array used for HTML select boxes
 *
 * @param Model $Model Model using this behavior
 * @param string|array $conditions SQL conditions as a string or as an array('field' =>'value',...)
 * @param string $keyPath A string path to the key, i.e. "{n}.Post.id"
 * @param string $valuePath A string path to the value, i.e. "{n}.Post.title"
 * @param string $spacer The character or characters which will be repeated
 * @param int $recursive The number of levels deep to fetch associated records
 * @return array An associative array of records, where the id is the key, and the display field is the value
 * @link http://book.cakephp.org/2.0/en/core-libraries/behaviors/tree.html#TreeBehavior::generateTreeList
 * @codingStandardsIgnoreStart
 */
	public function generateTreeList(Model $Model, $conditions = null, $keyPath = null, $valuePath = null, $spacer = '_', $recursive = null) {
		// @codingStandardsIgnoreEnd
		$Model->loadModels([
			'PagesLanguage' => 'Pages.PagesLanguage',
		]);

		$recursive = 0;

		if (isset($Model->belongsTo['Room'])) {
			$Model->PagesLanguage->useDbConfig = $Model->useDbConfig;
			$pageLangConditions = $Model->PagesLanguage->getConditions(array(
				'PagesLanguage.page_id = Page.id',
			), true);

			$Model->bindModel(array(
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
					'PagesLanguage' => array(
						'className' => 'Pages.PagesLanguage',
						'foreignKey' => false,
						'conditions' => array(
							'PagesLanguage.page_id = Page.id',
						),
						'fields' => '',
						'order' => ''
					),
					'OriginPagesLanguage' => array(
						'className' => 'Pages.PagesLanguage',
						'foreignKey' => false,
						'conditions' => array(
							'PagesLanguage.page_id = OriginPagesLanguage.page_id',
							'OriginPagesLanguage.language_id' => Current::read('Language.id'),
						),
						'fields' => '',
						'order' => ''
					),
				)
			), false);

			if ($conditions) {
				$conditions = Hash::merge($pageLangConditions, $conditions);
			} else {
				$conditions = $pageLangConditions;
			}
		}

		$results = parent::generateTreeList(
			$Model, $conditions, $keyPath, $valuePath, $spacer, $recursive
		);

		if (isset($Model->belongsTo['Room'])) {
			$Model->unbindModel(
				array('belongsTo' => array('Space', 'PagesLanguage', 'OriginPagesLanguage'))
			);
		}

		return $results;
	}

/**
 * 破損したツリーを復元する
 *
 * @param Model $model 呼び出し元のModel
 * @param string $mode parentのみ
 * @param null $missingParentAction 使用しない
 * @return bool true on success, false on failure
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @link https://book.cakephp.org/2.0/ja/core-libraries/behaviors/tree.html#TreeBehavior::recover
 */
	public function recover(Model $model, $mode = 'parent', $missingParentAction = null) {
		$settings = $this->settings[$model->alias];

		if (! $model->hasField($settings['parent']) &&
				! $model->hasField($settings['weight']) &&
				! $model->hasField($settings['sort_key']) &&
				! $model->hasField($settings['child_count'])) {
			return true;
		}

		$trees = $model->find('all', [
			'recursive' => -1,
			'fields' => [$model->primaryKey, $settings['parent'], $settings['weight']],
			'order' => [
				$settings['parent'] => 'asc',
				$settings['weight'] => 'asc',
				$model->primaryKey => 'asc',
			],
		]);

		$maxWeights = $model->find('all', [
			'recursive' => -1,
			'fields' => [$settings['parent'], 'Max(' . $settings['weight'] . ')'],
			'group' => [
				$settings['parent'],
			],
		]);

		$weights = [];
		foreach ($maxWeights as $weight) {
			$parentId = (string)$weight[$model->alias][$settings['parent']];

			//if (isset($weight[0]['Max(weight)'])) {
			//	$weights[$parentId] = (int)$weight[0]['Max(weight)'];
			//} else {
				$weights[$parentId] = 0;
			//}
		}

		$recovers = [];
		foreach ($trees as $tree) {
			$parentId = $tree[$model->alias][$settings['parent']];
			$primaryId = $tree[$model->alias][$model->primaryKey];

			if ($parentId === $primaryId) {
				$parentId = '1';
			}

			//if ($tree[$model->alias][$settings['weight']]) {
			//	$weight = $tree[$model->alias][$settings['weight']];
			//} else {
				$weights[$parentId]++;
				$weight = $weights[$parentId];
			//}

			if (! $parentId) {
				$sortKey = $this->_convertWeightToSortKey($weight, false, false);
			} else {
				if (! isset($recovers[$parentId])) {
					continue;
				}
				$sortKey = $this->_convertWeightToSortKey($weight, $recovers[$parentId]['sort_key'], true);
			}
			$recovers[$primaryId] = [
				'parent_id' => $parentId,
				'weight' => $weight,
				'sort_key' => $sortKey,
				'child_count' => 0,
			];

			$this->__countUpForRecover($recovers, $parentId);

			$weights[$parentId] = $weight;
		}
CakeLog::debug(__METHOD__ . '(' . __LINE__ . ') ' . var_export($recovers, true));

		$this->__updateRecovers($model, $recovers);

		return true;
	}

/**
 * 子供の件数のUP
 *
 * @param array &$recovers データ配列
 * @param int $parentId 親ID
 * @return void
 */
	private function __countUpForRecover(&$recovers, $parentId) {
		if (! $parentId) {
			return;
		}
		if (isset($recovers[$parentId])) {
			$recovers[$parentId]['child_count']++;
		}
		$this->__countUpForRecover($recovers, $recovers[$parentId]['parent_id']);
	}

/**
 * CakeのTreeビヘイビアからNC用のTreeビヘイビアのデータ構成にマイグレーションする
 *
 * @param Model $model Model using this behavior
 * @param array $recovers リカバリーデータ
 * @return bool
 * @throws InternalErrorException
 */
	private function __updateRecovers(Model $model, $recovers) {
		$escapeFields = $this->_escapeFields[$model->alias];

		$model->unbindModel(['belongsTo' => array_keys($model->belongsTo)]);
		foreach ($recovers as $primaryId => $recover) {
			if (! empty($recover['parent_id'])) {
				$update = [
					'Page.parent_id' => $recover['parent_id'],
					$escapeFields['weight'] => $recover['weight'],
					$escapeFields['sort_key'] => '\'' . $recover['sort_key'] . '\'',
					$escapeFields['child_count'] => $recover['child_count'],
				];
			} else {
				$update = [
					$escapeFields['weight'] => $recover['weight'],
					$escapeFields['sort_key'] => '\'' . $recover['sort_key'] . '\'',
					$escapeFields['child_count'] => $recover['child_count'],
				];
			}
			$conditions = [
				$escapeFields['id'] => $primaryId
			];
			if (! $model->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}
		$model->resetAssociations();

		return true;
	}

}
