<div id="totalcontest-dashboard" class="wrap totalcontest-page" ng-app="dashboard">
    <h1 class="totalcontest-page-title"><?php _e( 'Dashboard', 'totalcontest' ); ?></h1>

    <div class="totalcontest-page-tabs">
        <div class="totalcontest-page-tabs-item active" tab-switch="dashboard>overview">
			<?php _e( 'Overview', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item" tab-switch="dashboard>get-started">
			<?php _e( 'Get started', 'totalcontest' ); ?>
        </div>
        <a class="totalcontest-page-tabs-item" href="<?php echo esc_attr( $this->env['links.changelog'] ) ?>" target="_blank">
			<?php _e( 'What\'s new', 'totalcontest' ); ?>
        </a>
        <div class="totalcontest-page-tabs-item" tab-switch="dashboard>support">
			<?php _e( 'Support', 'totalcontest' ); ?>
        </div>
        <div class="totalcontest-page-tabs-item" tab-switch="dashboard>credits">
			<?php _e( 'Credits', 'totalcontest' ); ?>
        </div>
        
    </div>

    <div class="totalcontest-row">
        <div class="totalcontest-column">
            <div tab="dashboard>overview" class="active">
                <dashboard-overview></dashboard-overview>
            </div>

            <div tab="dashboard>get-started">
                <dashboard-get-started></dashboard-get-started>
            </div>

            <div tab="dashboard>activation">
                <dashboard-activation></dashboard-activation>
            </div>

            <div tab="dashboard>support">
                <dashboard-support></dashboard-support>
            </div>

            <div tab="dashboard>credits">
                <dashboard-credits></dashboard-credits>
            </div>

            <div tab="dashboard>my-account">
                <dashboard-my-account></dashboard-my-account>
            </div>

            <div class="totalcontest-box totalcontest-box-totalsuite">
                <div class="totalcontest-row">
                    <div class="totalcontest-column">
                        <div class="totalcontest-box-totalsuite-content">
                            <div class="totalcontest-box-title"><?php _e( 'Simple yet Powerful Plugins for WordPress', 'totalcontest' ); ?></div>
                            <div class="totalcontest-box-description"><?php _e( 'A suite of robust, maintained and secure plugins for WordPress that helps you generate more value for your business.', 'totalcontest' ); ?></div>
					        <?php
					        $url = add_query_arg(
						        [
							        'utm_source'   => 'in-app',
							        'utm_medium'   => 'totalsuite-box',
							        'utm_campaign' => 'totalcontest',
						        ],
						        $this->env['links.totalsuite']
					        );
					        ?>
                            <a href="<?php echo esc_attr( $url ); ?>" target="_blank" class="button button-primary button-large"><?php _e( 'Get Started', 'totalcontest' ); ?></a>
                        </div>
                    </div>
                    <div class="totalcontest-column">
                        <div class="totalcontest-box-totalsuite-image">
                            <img src="<?php echo esc_attr( $this->env['url'] ); ?>assets/dist/images/general/totalsuite.svg">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="totalcontest-column totalcontest-column-sidebar">
            <dashboard-subscribe></dashboard-subscribe>
            <dashboard-review></dashboard-review>
            <dashboard-translate></dashboard-translate>
        </div>
    </div>

    <!-- Templates -->
	<?php include __DIR__ . '/overview.php'; ?>
	<?php include __DIR__ . '/get-started.php'; ?>
	<?php include __DIR__ . '/activation.php'; ?>
	<?php include __DIR__ . '/my-account.php'; ?>
	<?php include __DIR__ . '/support.php'; ?>
	<?php include __DIR__ . '/credits.php'; ?>
	<?php include __DIR__ . '/sidebar.php'; ?>

</div>
