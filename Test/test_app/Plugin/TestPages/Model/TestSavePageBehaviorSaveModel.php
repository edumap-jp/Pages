<?php
/**
 * SavePageBehavior::save()テスト用Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * SavePageBehavior::save()テスト用Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Pages\Test\test_app\Plugin\TestPages\Model
 */
class TestSavePageBehaviorSaveModel extends AppModel {

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/en/models/model-attributes.html#usetable
 */
	public $useTable = 'pages';

/**
 * Alias name for model.
 *
 * @var string
 */
	public $alias = 'Page';

/**
 * 使用ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Pages.SavePage',
		'Pages.GetPage',
	);

}
