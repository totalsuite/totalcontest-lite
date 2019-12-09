<div class="totalcontest-design-sidebar">
    <div class="totalcontest-design-template">
        <div class="totalcontest-design-template-name">
            <small>
				<?php _e( 'Active template', 'totalcontest' ); ?>
            </small>
            <strong>{{ $ctrl.getCurrentTemplate('name') }}</strong>
        </div>

        <div class="totalcontest-design-template-button">
            <button class="button" type="button" ng-click="$ctrl.popToRoot(); $ctrl.setActiveTab('templates', '<?php echo esc_js( __( 'Templates', 'totalcontest' ) ); ?>')" ng-disabled="$ctrl.hasActiveTab('templates')">
				<?php _e( 'Change', 'totalcontest' ); ?>
            </button>
        </div>
    </div>

    <div class="totalcontest-design-controller">
        <div class="totalcontest-design-tabs-header">
            <div class="totalcontest-design-tabs-header-back" ng-class="{'active': $ctrl.hasActiveTab()}" ng-click="$ctrl.popActiveTab()"></div>
            <div class="totalcontest-design-tabs-header-title">
                {{ $ctrl.getActiveTabBreadcrumb() || '<?php echo esc_js( __( 'Customize', 'totalcontest' ) ); ?>' }}
            </div>
            <div class="totalcontest-design-tabs-header-buttons" ng-if="$ctrl.isActiveTab('advanced.advanced-template-settings')">
                <button class="button button-small" type="button" ng-click="$ctrl.resetToDefaults($ctrl.settings.template)">
					<?php _e( 'Reset', 'totalcontest' ); ?>
                </button>
            </div>
        </div>

        <div class="totalcontest-design-tabs-wrapper">
            <customizer-tabs>
			    <?php
			    /**
			     * Fires before design settings tabs.
			     *
			     * @since 2.0.0
			     */
			    do_action( 'totalcontest/actions/before/admin/editor/design/tabs', $this );
			    ?>
			    <?php foreach ( $designTabs as $designTabId => $designTab ): ?>
                    <customizer-tab target="<?php echo esc_attr( $designTabId ); ?>"><?php echo esc_html( $designTab['label'] ); ?></customizer-tab>
			    <?php endforeach; ?>
			    <?php
			    /**
			     * Fires after design settings tabs.
			     *
			     * @since 2.0.0
			     */
			    do_action( 'totalcontest/actions/after/admin/editor/design/tabs', $this );
			    ?>
            </customizer-tabs>

		    <?php foreach ( $designTabs as $designTabId => $designTab ): ?>
                <customizer-tab-content name="<?php echo esc_attr( $designTabId ); ?>" class="totalcontest-design-tabs-content-<?php echo esc_attr( $designTabId ); ?>">
				    <?php
				    /**
				     * Fires before design settings tab content.
				     *
				     * @since 2.0.0
				     */
				    do_action( "totalcontest/actions/before/admin/editor/design/tabs/content/{$designTabId}" );

				    $path = empty( $designTab['file'] ) ? __DIR__ . "/tabs/{$designTabId}.php" : $designTab['file'];
				    if ( file_exists( $path ) ):
					    include_once $path;
				    endif;

				    /**
				     * Fires after design settings tab content.
				     *
				     * @since 2.0.0
				     */
				    do_action( "totalcontest/actions/after/admin/editor/design/tabs/content/{$designTabId}" );
				    ?>
                </customizer-tab-content>
		    <?php endforeach; ?>
        </div>
    </div>
    <div class="totalcontest-design-devices">
        <div class="totalcontest-design-devices-item"
             ng-class="{'active': $ctrl.isDevice('laptop')}"
             ng-click="$ctrl.setDevice('laptop')">
            <span class="dashicons dashicons-desktop"></span>
        </div>
        <div class="totalcontest-design-devices-item"
             ng-class="{'active': $ctrl.isDevice('tablet')}"
             ng-click="$ctrl.setDevice('tablet')">
            <span class="dashicons dashicons-tablet"></span>
        </div>
        <div class="totalcontest-design-devices-item"
             ng-class="{'active': $ctrl.isDevice('smartphone')}"
             ng-click="$ctrl.setDevice('smartphone')">
            <span class="dashicons dashicons-smartphone"></span>
        </div>
    </div>
</div>