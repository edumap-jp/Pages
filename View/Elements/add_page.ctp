<?php
/**
 * Setting menu element.
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */
?>

<div class="modal fade" id="page-add" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<?php echo __d('pages', 'Add page'); ?>
			</div>

			<div class="modal-body">
				<div class="panel panel-default">
					<?php echo $this->Form->create('Page', array(
							'name' => 'addForm',
							'novalidate' => true,
							'action' => 'add/' . $formPage['page']['roomId'] . '/' . $formPage['page']['id']
						)); ?>

						<div class="panel-body has-feedback">
							<?php echo $this->element('Pages/edit_form', array('action' => $action)); ?>
						</div>

						<div class="panel-footer text-center">
							<button type="button" class="btn btn-default btn-workflow" onclick="location.href = '<?php echo $cancelUrl; ?>'">
								<span class="glyphicon glyphicon-remove"></span>
								<?php echo __d('net_commons', 'Cancel'); ?>
							</button>

							<?php echo $this->Form->button(__d('net_commons', 'OK'), array(
									'class' => 'btn btn-primary btn-workflow',
									'name' => 'save',
								)); ?>
						</div>
					<?php echo $this->Form->end(); ?>
				</div>
			</div>

			<?php echo $this->Form->end(); ?>
		</div>
	</div>
</div>
