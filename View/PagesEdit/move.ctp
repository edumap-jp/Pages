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
<?php echo __d('pages', 'Select move page'); ?>
<?php $this->end(); ?>

<?php echo $this->NetCommonsForm->create('Page', array(
		'type' => 'put',
		'url' => NetCommonsUrl::actionUrlAsArray(array('action' => 'move', Current::read('Room.id')))
	)); ?>

	<?php echo $this->NetCommonsForm->hidden('_NetCommonsUrl.redirect'); ?>
	<?php echo $this->NetCommonsForm->hidden('Page.id', array('value' => Current::read('Page.id'))); ?>
	<?php echo $this->NetCommonsForm->hidden('Page.room_id', array('value' => Current::read('Room.id'))); ?>
	<?php echo $this->NetCommonsForm->hidden('Room.id', array('value' => Current::read('Room.id'))); ?>
	<?php echo $this->NetCommonsForm->unlockField('Page.parent_id'); ?>
	<?php echo $this->NetCommonsForm->hidden('Page.type', array('value' => 'move')); ?>

	<?php echo $this->MessageFlash->description(
			__d('pages', 'Please select the destination of the "%s" page.', Hash::get($pages, Current::read('Page.id') . '.PagesLanguage.name'))
		); ?>

	<?php if ($treeList) : ?>
		<table class="table table-hover">
			<tbody>
				<?php foreach ($treeList as $pageId) : ?>
				<tr ng-class="{active: pageParentId === '<?php echo $pageId; ?>'}">
					<td>
						<?php echo $this->PagesEdit->indent($pageId); ?>

						<?php if (Current::read('Page.parent_id') !== (string)$pageId) : ?>
							<?php echo $this->NetCommonsForm->radio('Page.parent_id',
									array($pageId => Hash::get($pages, $pageId . '.PagesLanguage.name')),
									array(
										'hiddenField' => false,
										'ng-click' => 'pageParentId = \'' . $pageId . '\'',
										'div' => array('class' => 'form-inline page-move-radio'),
									)
								); ?>
						<?php else : ?>
							<strong>
								<?php echo h(Hash::get($pages, $pageId . '.PagesLanguage.name')); ?>
							</strong>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="text-center">
			<button name="cancel" type="button" ng-disabled="sending" ng-click="cancel()" class="btn btn-default btn-workflow">
				<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				<?php echo __d('net_commons', 'Cancel'); ?>
			</button>
			<button type="submit" ng-disabled="(sending || ! pageParentId)" class="btn btn-primary btn-workflow" name="save">
				<?php echo __d('net_commons', 'OK'); ?>
			</button>
		</div>
	<?php else : ?>
		<article class="not-found">
			<?php echo __d('pages', 'Page not found.'); ?>
		</article>

		<div class="text-center">
			<button name="cancel" type="button" ng-disabled="sending" ng-click="cancel()" class="btn btn-default btn-workflow">
				<span class="glyphicon glyphicon-remove"></span> <?php echo __d('net_commons', 'Close'); ?>
			</button>
		</div>
	<?php endif; ?>

<?php echo $this->NetCommonsForm->end();
