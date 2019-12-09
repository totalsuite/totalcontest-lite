<div id="totalcontest-modules" class="wrap totalcontest-page" ng-app="modules" ng-controller="ModulesCtrl as modules">
    <h1 class="totalcontest-page-title">
		<?php _e( 'Extensions', 'totalcontest' ); ?>
        <button type="button" ng-click="modules.toggleInstaller()" class="page-title-action" role="button"><span class="upload"><?php _e( 'Upload', 'totalcontest' ); ?></span></button>
    </h1>
    <modules-installer ng-if="modules.isInstallerVisible()"></modules-installer>
    <div class="totalcontest-page-tabs">
        <div class="totalcontest-page-tabs-item active" tab-switch="modules>installed">
			<?php _e( 'Installed', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item" tab-switch="modules>store">
			<?php _e( 'Store', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item right" ng-click="modules.refresh()">
            <span class="dashicons dashicons-update"></span>
			<?php _e( 'Refresh', 'totalcontest' ); ?>
        </div>
    </div>
    <modules-manager type="'extensions'"></modules-manager>

	<?php include __DIR__ . '/../../views/install.php'; ?>
	<?php include __DIR__ . '/../../views/manager.php'; ?>
</div>
