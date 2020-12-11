<?php
/**
 * 移動画面View
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('pages', 'Add m17n page'); ?>
<?php $this->end(); ?>

<?php echo $this->NetCommonsForm->create('PagesLanguage', array(
		'type' => 'post',
		'url' => NetCommonsUrl::actionUrlAsArray(array('action' => 'add_m17n', 'key' => Current::read('Room.id'), Current::read('Page.id')))
	)); ?>

	<?php echo $this->NetCommonsForm->hidden('_NetCommonsUrl.redirect'); ?>
	<?php echo $this->NetCommonsForm->hidden('Page.id', array('value' => Current::read('Page.id'))); ?>
	<?php echo $this->NetCommonsForm->hidden('Page.room_id', array('value' => Current::read('Room.id'))); ?>
	<?php echo $this->NetCommonsForm->hidden('Room.id', array('value' => Current::read('Room.id'))); ?>

	<?php echo $this->NetCommonsForm->hidden('PagesLanguage.page_id'); ?>
	<?php echo $this->NetCommonsForm->input('PagesLanguage.language_id', array(
			'type' => 'select',
			'label' => __d('pages', 'Select language'),
			'options' => $disusedLangs,
		)); ?>

	<div class="text-center">
		<button name="cancel" type="button" ng-disabled="sending" ng-click="cancel()" class="btn btn-default btn-workflow">
			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			<?php echo __d('net_commons', 'Cancel'); ?>
		</button>

		<button type="submit" ng-disabled="sending" class="btn btn-primary btn-workflow" name="save">
			<?php echo __d('net_commons', 'OK'); ?>
		</button>
	</div>

<?php echo $this->NetCommonsForm->end();
