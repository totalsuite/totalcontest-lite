<div class="totalcontest-tabs-container">
    <div class="totalcontest-tab-content active">
        <div class="totalcontest-settings-item">
            <div class="totalcontest-settings-field">
                <label>
                    <input type="checkbox" name="" ng-model="$ctrl.options.performance.async.enabled">
					<?php _e( 'Asynchronous loading', 'totalcontest' ); ?>
                </label>

                <p class="totalcontest-feature-tip"><?php _e( 'This can be useful when you would like to bypass cache mechanisms and plugins.', 'totalcontest' ); ?></p>
            </div>
        </div>
        <div class="totalcontest-settings-item">
            <div class="totalcontest-settings-field">
                <label>
                    <input type="checkbox" name="" disabled>
					<?php _e( 'Full checks on page load.', 'totalcontest' ); ?>
                    <?php TotalContest( 'upgrade-to-pro' ); ?>
                </label>

                <p class="totalcontest-feature-tip"><?php _e( 'This may put high load on your server because TotalContest will hit the database frequently.', 'totalcontest' ); ?></p>
            </div>
        </div>
        <div class="totalcontest-settings-item">
            <div class="totalcontest-settings-field">
                <button type="button" class="button" ng-click="$ctrl.purge('cache')" ng-disabled="$ctrl.isPurging('cache') || $ctrl.isPurged('cache')">
                    <span ng-if="$ctrl.isPurgeReady('cache')"><?php _e( 'Clear cache', 'totalcontest' ); ?></span>
                    <span ng-if="$ctrl.isPurging('cache')"><?php _e( 'Clearing', 'totalcontest' ); ?></span>
                    <span ng-if="$ctrl.isPurged('cache')"><?php _e( 'Cleared', 'totalcontest' ); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>