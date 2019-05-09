<?php
/**
 * PageContainer Model
 *
 * @property Page $Page
 * @property Box $Box
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesAppModel', 'Pages.Model');

/**
 * PageContainer Model
 */
class PageContainer extends PagesAppModel {

/**
 * constant value
 */
	const TYPE_HEADER = '1';
	const TYPE_MAJOR = '2';
	const TYPE_MAIN = '3';
	const TYPE_MINOR = '4';
	const TYPE_FOOTER = '5';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Page' => array(
			'className' => 'Pages.Page',
			'foreignKey' => 'page_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = ValidateMerge::merge($this->validate, array(
			'is_configured' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		return parent::beforeValidate($options);
	}
/**
 * Save page each association model
 *
 * @param array $data request data
 * @throws InternalErrorException
 * @return mixed On success Model::$data if its not empty or true, false on failure
 */
	public function savePageContainer($data) {
		//トランザクションBegin
		$this->begin();

		if (! $this->validateMany($data['PageContainer'])) {
			return false;
		}
		try {
			if (! $this->saveMany($data['PageContainer'], ['validate' => false])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			if (Hash::get($data, 'ChildPage.id')) {
				$childPageId = explode(',', Hash::get($data, 'ChildPage.id', ''));

				$containerPages = Hash::get($data, 'PageContainer');
				$containerTypes = array(
					Container::TYPE_HEADER, Container::TYPE_MAJOR, Container::TYPE_MINOR, Container::TYPE_FOOTER
				);
				foreach ($containerTypes as $containerType) {
					$updated = array(
						'PageContainer.is_published' => Hash::get(
							$containerPages, $containerType . '.PageContainer.is_published', true
						),
					);
					$conditions = array(
						'PageContainer.is_configured' => false,
						'PageContainer.page_id' => $childPageId,
						'PageContainer.container_type' => $containerType,
					);

					$result = $this->updateAll($updated, $conditions);
					if (! $result) {
						throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					}
				}
			}

			$this->commit();

		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		return true;
	}

}
