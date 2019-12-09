///<reference path="../../common/decorators.ts"/>
namespace TotalCore {
    import Component = TotalCore.Common.Component;
    import IScope = angular.IScope;
    import Processable = TotalCore.Common.Processable;
    import RepositoryService = TotalCore.Dashboard.Providers.RepositoryService;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-my-account-component-template',
        bindings: {}
    })
    class DashboardMyAccountComponent extends Processable {
        public account = {
            access_token: this.SettingsService.account['access_token'] || '',
            email: this.SettingsService.account['email'] || '',
            status: this.SettingsService.account['status'] || false,
        };
        public error: string;

        public constructor(public $scope: IScope, public RepositoryService: RepositoryService, public SettingsService: SettingsService) {
            super();
        }

        $onInit() {
            window.addEventListener(
                'message',
                (event) => {
                    if (event.data.totalsuite && event.data.totalsuite.auth.access_token) {
                        this.$scope.$applyAsync(() => {
                            this.account.access_token = event.data.totalsuite.auth.access_token;
                            this.validate();
                        });
                    }
                },
                false
            );
        }

        openSignInPopup(url) {
            window.open(url, 'popup', 'width=600,height=600');
        }

        validate() {
            this.startProcessing();
            this.error = null;
            this.RepositoryService.postAccount(this.account)
                .then((response) => {
                    if (response.success) {
                        this.account.status = true;
                        this.account.email = response.data.email;
                    } else {
                        this.error = response.data;
                    }
                })
                .catch((error) => {
                    this.error = error.statusText;
                })
                .finally(() => this.stopProcessing());
        }
    }
}