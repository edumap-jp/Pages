<?php
/**
 * PagesLanguage Model
 *
 * @property Language $Language
 * @property Page $Page
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PagesAppModel', 'Pages.Model');

/**
 * PagesLanguage Model
 */
class PagesLanguage extends PagesAppModel {

/**
 * `<title></title>`のデフォルト値
 *
 * @var const
 */
	const DEFAULT_META_TITLE = '{X-PAGE_NAME} - {X-SITE_NAME}';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
	);

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
		),
		'Language' => array(
			'className' => 'M17n.Language',
			'foreignKey' => 'language_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		//多言語
		'M17n.M17n' => array(
			'keyField' => 'page_id'
		),
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
			'name' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Page name')),
					'required' => false
				),
			),
			'page_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'language_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
			'meta_title' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('pages', 'Title tag')),
					'required' => false
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * Called after data has been checked for errors
 *
 * @return void
 */
	public function afterValidate() {
		if (Hash::get($this->data, $this->alias . '.meta_title') === self::DEFAULT_META_TITLE) {
			$this->data = Hash::insert($this->data, $this->alias . '.meta_title', '');
		}
	}

/**
 * ページデータ取得
 *
 * @param int $pageId pages.id
 * @param int $languageId languages.id
 * @return array
 */
	public function getPagesLanguage($pageId, $languageId) {
		$conditions = array(
			'PagesLanguage.page_id' => $pageId,
			'PagesLanguage.language_id' => $languageId,
		);

		$pagesLanguage = $this->find('first', array(
			'recursive' => 0,
			'conditions' => $conditions,
		));

		return $pagesLanguage;
	}

/**
 * ページデータ取得条件配列
 *
 * @param array $addConditions 追加条件
 * @param bool $reset リセットフラグ
 * @return array
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function getConditions($addConditions = array(), $reset = true) {
		$this->bindPagesLanguage($reset);

		$conditions = Hash::merge(array(
			'OR' => array(
				'PagesLanguage.language_id' => Current::read('Language.id'),
				array(
					'Space.is_m17n' => false,
					'OR' => array(
						'PagesLanguage.id = OriginPagesLanguage.id',
						'OriginPagesLanguage.id' => null,
					)
				)
			)
		), $addConditions);

		return $conditions;
	}

/**
 * PagesLanguageのバインド
 *
 * @param bool $reset リセットフラグ
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function bindPagesLanguage($reset = true) {
		$this->bindModel(array(
			'belongsTo' => array(
				'Room' => array(
					'className' => 'Rooms.Room',
					'foreignKey' => false,
					'conditions' => array(
						'Page.room_id = Room.id',
					),
					'fields' => '',
					'order' => ''
				),
				'Space' => array(
					'className' => 'Rooms.Space',
					'foreignKey' => false,
					'conditions' => array(
						'Room.space_id = Space.id',
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
		), $reset);
	}

/**
 * PagesLanguageのunbind
 *
 * @return void
 */
	public function unbindPagesLanguage() {
		$this->unbindModel(array('belongsTo' => array('Room', 'Space', 'OriginPagesLanguage')));
	}

/**
 * 言語ページでデータ登録処理
 *
 * @param array $data リクエストデータ
 * @throws InternalErrorException
 * @return bool
 */
	public function savePagesLanguage($data) {
		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			return false;
		}

		try {
			if (! $this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$this->commit();

		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		return true;
	}

/**
 * 言語ページでデータ登録処理
 *
 * @param array $data リクエストデータ
 * @return bool
 */
	public function saveM17nPage($data) {
		$conditions = array(
			'PagesLanguage.page_id' => $data['Page']['id'],
			'PagesLanguage.language_id' => Current::read('Language.id'),
		);
		$pagesLanguage = $this->find('first', array(
			'recursive' => -1,
			'conditions' => $conditions,
		));
		$data['PagesLanguage']['name'] = $pagesLanguage['PagesLanguage']['name'];

		$langId = Current::read('Language.id');

		Current::write('Language.id', $data['PagesLanguage']['language_id']);

		$result = $this->savePagesLanguage($data['PagesLanguage']);

		Current::write('Language.id', $langId);

		return $result;
	}

/**
 * 当言語ページの削除
 *
 * @param array $data request data
 * @throws InternalErrorException
 * @return mixed On success Model::$data if its not empty or true, false on failure
 */
	public function deletePageLanguage($data) {
		//トランザクションBegin
		$this->begin();

		try {
			//PagesLanguageの削除
			if (! $this->delete($data['PagesLanguage']['id'])) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//is_originの付け替え
			$update = array();
			$conditions = array();
			$pageCount = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'PagesLanguage.page_id' => $data['Page']['id']
				),
			));
			if ($pageCount === 1) {
				$update = array(
					'is_origin' => true,
					'is_translation' => false
				);
				$conditions = array(
					'page_id' => $data['Page']['id']
				);
			} else {
				$isOrigin = $this->find('count', array(
					'recursive' => -1,
					'conditions' => array(
						'PagesLanguage.page_id' => $data['Page']['id'],
						'PagesLanguage.is_origin' => true
					),
				));
				if ($isOrigin) {
					$this->commit();
					return true;
				}
				$pageLang = $this->find('first', array(
					'recursive' => 0,
					'conditions' => array(
						'PagesLanguage.page_id' => $data['Page']['id'],
						'Language.is_active' => true
					),
					'order' => array('Language.weight' => 'asc')
				));

				$update = array(
					'is_origin' => true,
				);
				$conditions = array(
					'id' => $pageLang['PagesLanguage']['id']
				);
			}
			if (! $this->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			$this->commit();

		} catch (Exception $ex) {
			$this->rollback($ex);
		}

		return true;
	}

}
