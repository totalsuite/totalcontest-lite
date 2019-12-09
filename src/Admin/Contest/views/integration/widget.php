<div class="totalcontest-integration-steps" ng-controller="SidebarIntegrationCtrl as sidebarIntegrationCtrl">
    <div class="totalcontest-integration-steps-item">
        <div class="totalcontest-integration-steps-item-number">
            <div class="totalcontest-integration-steps-item-number-circle">1</div>
        </div>
        <div class="totalcontest-integration-steps-item-content">
            <h3 class="totalcontest-h3">
                <?php _e( 'Add it to sidebar', 'totalcontest' ); ?>
            </h3>
            <p>
                <?php _e( 'Start by adding this contest to one of available sidebars:', 'totalcontest' ); ?>
            </p>
            <div class="totalcontest-integration-steps-item-copy">
                <select name="" ng-model="sidebarIntegrationCtrl.sidebar" ng-options="sidebar as sidebar.name for sidebar in sidebarIntegrationCtrl.sidebars">
                    <option value=""><?php _e('Select a sidebar', 'totalcontest'); ?></option>
                </select>
                <button type="button" class="button button-primary button-large" ng-disabled="!sidebarIntegrationCtrl.sidebar || sidebarIntegrationCtrl.sidebar.inserted"
                        ng-click="sidebarIntegrationCtrl.addWidgetToSidebar()">
                    <span ng-if="!sidebarIntegrationCtrl.sidebar.inserted"><?php _e( 'Insert', 'totalcontest' ); ?></span>
                    <span ng-if="sidebarIntegrationCtrl.sidebar.inserted"><?php _e( 'Inserted', 'totalcontest' ); ?></span>
                </button>
            </div>
        </div>
    </div>
    <div class="totalcontest-integration-steps-item">
        <div class="totalcontest-integration-steps-item-number">
            <div class="totalcontest-integration-steps-item-number-circle">2</div>
        </div>
        <div class="totalcontest-integration-steps-item-content">
            <h3 class="totalcontest-h3">
                <?php _e( 'Preview', 'totalcontest' ); ?>
            </h3>
            <p>
                <?php _e( 'Open the page which you have the same sidebar and test contest functionality.', 'totalcontest' ); ?>
            </p>
        </div>
    </div>
</div>