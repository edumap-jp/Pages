<?php
/**
 * ページ全体 Layout
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$bodyCss = '';
if (!empty($modal)) {
	$bodyCss = 'modal-open';
}
/* 未ログイン時にはヘッダーメニューを表示しないこともカスタマイズでできるようにcss追加 */
if (AuthComponent::user()) {
	$bodyCss .= ' body-loggedin';
} else {
	$bodyCss .= ' body-nologgedin';
}
?>
<!DOCTYPE html>
<html lang="<?php echo Configure::read('Config.language') ?>" ng-app="NetCommonsApp">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title><?php echo (isset($pageTitle) ? h($pageTitle) : ''); ?></title>

		<?php
			echo $this->element('NetCommons.common_favicon');
			echo $this->fetch('meta');

			echo $this->element('NetCommons.common_css');
			echo $this->fetch('css');
			echo $this->element('NetCommons.common_theme_css');

			echo $this->element('NetCommons.common_js');
			//echo $this->element('Wysiwyg.mathjax_js');
			echo $this->fetch('script');
		?>
	</head>

	<body class="<?php echo $bodyCss; ?>" ng-controller="NetCommons.base">
		<?php echo $this->Flash->render(); ?>

		<?php echo $this->element('NetCommons.common_header', ['navbarStyle' => 'navbar-default']); ?>

		<main id="nc-container" class="<?php echo $pageContainerCss; ?>" ng-init="hashChange()">
			<?php echo $pageHeader; ?>

			<div class="row">
				<?php echo $pageContent; ?>

				<?php echo $pageMajor; ?>

				<?php echo $pageMinor; ?>
			</div>

			<?php echo $pageFooter; ?>
		</main>

		<?php echo $this->element('Pages.common_footer'); ?>
		<?php
			$trackingId = SiteSettingUtil::read('Matomo.tracking_id');
			if ($trackingId) {
				echo $this->element('Pages.matomo_tracking', ['trackingId' => $trackingId]);
			}
		?>

		<?php if (!empty($modal)) : ?>
			<div class="modal-backdrop fade in"></div>
		<?php endif; ?>
	</body>
</html>
