///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/helpers.ts"/>
///<reference path="../../common/providers/settings.ts"/>
///<reference path="../providers/repository.ts"/>
namespace TotalCore.Dashboard.Components {
    import RepositoryService = TotalCore.Dashboard.Providers.RepositoryService;
    import Component = TotalCore.Common.Component;
    import Processable = TotalCore.Common.Processable;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-activation-component-template',
        bindings: {}
    })
    class DashboardActivationComponent extends Processable {
        public activation = {
            status: this.SettingsService.activation['status'] || false,
            key: this.SettingsService.activation['key'] || '',
            email: this.SettingsService.activation['email'] || '',
        };
        public error: string;

        public constructor(public RepositoryService: RepositoryService, public SettingsService: SettingsService) {
            super();
        }

        validate() {
            this.startProcessing();
            this.error = null;
            this.RepositoryService.postActivation(this.activation)
                .then((response) => {
                    if (response.success) {
                        this.activation.status = true;
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