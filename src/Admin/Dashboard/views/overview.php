<script type="text/ng-template" id="dashboard-overview-component-template">
    <div class="totalcontest-box">
        <div class="totalcontest-create-contest" ng-if="$ctrl.contests != null && !$ctrl.contests.length">
            <img src="<?php echo esc_attr( $this->env['url'] ); ?>assets/dist/images/general/create-contest.svg">
            <div class="totalcontest-box-title"><?php _e( 'It\'s time to create your first contest.', 'totalcontest' ); ?></div>
            <div class="totalcontest-box-description"><?php _e( 'There are no contests yet.', 'totalcontest' ); ?></div>
            <a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=contest' ) ); ?>"
               class="button button-large button-primary"><?php _e( 'Create Contest', 'totalcontest' ); ?>
            </a>
        </div>
        <div class="totalcontest-overview" ng-class="{'totalcontest-processing': $ctrl.contests === null}">
            <div class="totalcontest-overview-item" ng-repeat="contest in $ctrl.contests">
                <a class="totalcontest-overview-item-segment totalcontest-overview-item-title" ng-href="{{ contest.editLink }}">
                    {{ contest.title || '<?php _e( 'Untitled', 'totalcontest' ); ?>' }}
                    <span class="totalcontest-overview-item-status" ng-class="{'active': contest.status == 'publish'}">
                        {{ contest.status === 'publish' ? '<?php _e( 'Live', 'totalcontest' ); ?>' : '<?php _e( 'Pending', 'totalcontest' ); ?>'}}
                    </span>
                </a>
                <a class="totalcontest-overview-item-segment totalcontest-overview-item-action" ng-href="{{ contest.permalink }}" target="_blank">
                    <span class="dashicons dashicons-admin-links"></span>
                </a>
                <a class="totalcontest-overview-item-segment totalcontest-overview-item-number" ng-href="{{ contest.logLink }}" target="_blank">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <span>{{contest.statistics.votes|number}} <?php _e( 'Votes', 'totalcontest' ); ?></span>
                </a>
                <a class="totalcontest-overview-item-segment totalcontest-overview-item-number" ng-href="{{ contest.submissionsLink }}" target="_blank">
                    <span class="dashicons dashicons-portfolio"></span>
                    <span>{{contest.statistics.submissions|number}} <?php _e( 'Submissions', 'totalcontest' ); ?></span>
                </a>
            </div>
        </div>
    </div>
</script>