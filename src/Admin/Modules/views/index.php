<div id="totalcontest-modules" class="wrap totalcontest-page" ng-app="modules">
    <h1 class="totalcontest-page-title"><?php _e( 'Modules', 'totalcontest' ); ?></h1>
    <div class="totalcontest-page-tabs">
        <div class="totalcontest-page-tabs-item active" tab-switch="modules>install">
			<?php _e( 'Install', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item" tab-switch="modules>templates">
			<?php _e( 'Templates', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item" tab-switch="modules>extensions">
			<?php _e( 'Extensions', 'totalcontest' ); ?>
        </div>
    </div>
    <modules-manager ng-show="isCurrentTab('modules>templates') || isCurrentTab('modules>extensions')" type="isCurrentTab('modules>templates') ? 'templates' : 'extensions'"></modules-manager>
    <modules-installer tab="modules>install" class="active"></modules-installer>

	<?php include __DIR__ . '/install.php'; ?>
	<?php include __DIR__ . '/manager.php'; ?>
</div>