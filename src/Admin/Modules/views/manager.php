<script type="text/ng-template" id="modules-manager-component-template">
    <div class="totalcontest-modules-list" ng-class="{'totalcontest-processing': $ctrl.modules === null}" ng-init="$root.$manager = $ctrl">
        <div class="totalcontest-row">
            <div class="totalcontest-column totalcontest-column-6" ng-repeat="module in $ctrl.getModules()" ng-if="($root.isCurrentTab('modules>installed') && module.isInstalled()) || ($root.isCurrentTab('modules>store') && !module.isInstalled())">
                <div class="totalcontest-modules-list-item"
                     ng-class="{'totalcontest-processing': $ctrl.isProcessing(module), 'totalcontest-successful': $ctrl.isSuccessful(module)}">
                    <div class="totalcontest-modules-list-item-error" ng-show="$ctrl.getError(module)" ng-click="$ctrl.dismissError(module)">
                        {{$ctrl.getError(module)}}

                        <button type="button" class="notice-dismiss"></button>
                    </div>

                    <div class="totalcontest-modules-list-item-body">
                        <div class="totalcontest-modules-list-item-image">
                            <img ng-src="{{module.getImage('icon')}}">
                        </div>
                        <div class="totalcontest-modules-list-item-details">
                            <div class="totalcontest-modules-list-item-title">
                                <a ng-href="{{module.getPermalink()}}" target="_blank">{{module.getName()}}</a>
                            </div>
                            <div class="totalcontest-modules-list-item-author"><?php _e( 'By', 'totalcontest' ); ?> <a ng-href="{{module.getAuthorUrl()}}">{{module.getAuthorName()}}</a>
                            </div>
                            <div class="totalcontest-modules-list-item-description">
                                <p ng-bind-html="module.get('description')"></p>
                            </div>
                        </div>
                        <div class="totalcontest-modules-list-item-actions">
                            <div ng-show="module.isPurchased() && !module.isInstalled()">
                                <button ng-click="$ctrl.applyAction('installFromStore', module)" class="widefat button button-primary button-large">
                                    <span class="dashicons dashicons-arrow-down-alt"></span> <?php _e( 'Install', 'totalcontest' ); ?>
                                </button>
                            </div>
                            <div ng-show="!module.isPurchased() && !module.isInstalled()">
                                <a target="_blank" ng-href="{{module.getPermalink()}}" class="widefat button button-primary button-large">
                                    <span class="dashicons dashicons-external"></span> <?php _e( 'Get it', 'totalcontest' ); ?>
                                </a>
                            </div>
                            <div ng-show="module.isInstalled() && module.hasUpdate()">
                                <button ng-click="$ctrl.applyAction('update', module)" class="widefat button button-primary button-large">
                                    <span class="dashicons dashicons-update"></span> <?php _e( 'Update', 'totalcontest' ); ?>
                                </button>
                            </div>
                            <div ng-show="module.isInstalled() && !module.isActivated()">
                                <button ng-click="$ctrl.applyAction('activate', module)" class="widefat button button-large">
                                    <span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Activate', 'totalcontest' ); ?>
                                </button>
                            </div>
                            <div ng-show="module.isInstalled() && !module.isActivated()">
                                <button ng-click="$ctrl.applyAction('uninstall', module)" class="widefat button button-danger button-large">
                                    <span class="dashicons dashicons-trash"></span> <?php _e( 'Uninstall', 'totalcontest' ); ?>
                                </button>
                            </div>
                            <div ng-show="module.isInstalled() && module.isActivated()">
                                <button class="widefat button button-danger button-large"
                                        ng-disabled="module.getId() === 'basic-template'"
                                        ng-click="$ctrl.applyAction('deactivate', module)">
                                    <span class="dashicons dashicons-warning"></span> <?php _e( 'Deactivate', 'totalcontest' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="totalcontest-modules-list-item-footer">
                        <div class="totalcontest-modules-list-item-version">
                            <strong><?php _e( 'Version', 'totalcontest' ); ?> {{module.getVersion()}}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</script>