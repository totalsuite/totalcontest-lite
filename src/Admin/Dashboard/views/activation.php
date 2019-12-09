<script type="text/ng-template" id="dashboard-activation-component-template">
    <div class="totalcontest-box totalcontest-box-activation">
        <div class="totalcontest-box-section">
            <div class="totalcontest-row">
                <div class="totalcontest-column">
                    <div class="totalcontest-box-content" ng-if="$ctrl.activation.status">
                        <img src="<?php echo esc_attr( $this->env['url'] ); ?>assets/dist/images/activation/updates-on.svg" class="totalcontest-box-activation-image">
                        <div class="totalcontest-box-title"><?php _e( 'Product activated!', 'totalcontest' ); ?></div>
                        <div class="totalcontest-box-description"><?php _e( 'You\'re now receiving updates.', 'totalcontest' ); ?></div>
                        <table class="wp-list-table widefat striped">
                            <tr>
                                <td><strong><?php _e( 'Activation code', 'totalcontest' ); ?></strong></td>
                            </tr>
                            <tr>
                                <td>{{$ctrl.activation.key}}</td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Licensed to', 'totalcontest' ); ?></strong></td>
                            </tr>
                            <tr>
                                <td>{{$ctrl.activation.email}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="totalcontest-box-content" ng-if="!$ctrl.activation.status">
                        <img src="<?php echo esc_attr( $this->env['url'] ); ?>assets/dist/images/activation/updates-off.svg" class="totalcontest-box-activation-image">
                        <div class="totalcontest-box-title"><?php printf( __( 'Product activation for %s', 'totalcontest' ), $this->env['domain'] ); ?></div>
                        <div class="totalcontest-box-description"><?php _e( 'Open <a target="_blank" href="https://codecanyon.net/downloads">downloads page</a>, find the product, click "Download" then select on "License certificate & purchase code (text)".', 'totalcontest' ); ?></div>
                        <div class="totalcontest-box-composed-form-error" ng-if="$ctrl.error">{{$ctrl.error}}</div>
                        <form class="totalcontest-box-composed-form" ng-submit="$ctrl.validate()">
                            <input type="text" class="totalcontest-box-composed-form-field" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" ng-model="$ctrl.activation.key">
                            <input type="email" class="totalcontest-box-composed-form-field" placeholder="email@domain.tld" ng-model="$ctrl.activation.email">
                            <button type="submit" class="button button-primary button-large totalcontest-box-composed-form-button" ng-disabled="!$ctrl.activation.key || !$ctrl.activation.email || $ctrl.isProcessing()">{{
                                $ctrl.isProcessing() ? '<?php _e( 'Activating', 'totalcontest' ); ?>' : '<?php _e( 'Activate', 'totalcontest' ); ?>' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="totalcontest-column">
                    <img src="<?php echo esc_attr( $this->env['url'] ); ?>assets/dist/images/activation/how-to.svg" alt="Get license code">
                </div>
            </div>
        </div>
    </div>
</script>