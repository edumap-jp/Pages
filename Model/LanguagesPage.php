<?php
/**
 * LanguagesPage Model
 *
 * @property Page $Page
 * @property Language $Language
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('AppModel', 'Model');

/**
 * Summary for LanguagesPage Model
 */
class LanguagesPage extends AppModel {

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
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
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
	public function getLanguagesPage($pageId, $languageId) {
		$conditions = array(
			'LanguagesPage.page_id' => $pageId,
			'LanguagesPage.language_id' => $languageId,
		);

		$languagesPage = $this->find('first', array(
			'recursive' => 0,
			'conditions' => $conditions,
		));

		return $languagesPage;
	}

/**
 * 言語ページでデータ登録処理
 *
 * @param array $data リクエストデータ
 * @throws InternalErrorException
 * @return bool
 */
	public function saveLanguagesPage($data) {
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

}
