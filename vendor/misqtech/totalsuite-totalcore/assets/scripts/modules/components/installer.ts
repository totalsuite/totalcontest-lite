///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/helpers.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Modules.Components {
    import Component = TotalCore.Common.Component;
    import Progressive = TotalCore.Common.Progressive;
    import RepositoryService = TotalCore.Modules.Providers.RepositoryService;
    import IRootScopeService = angular.IRootScopeService;

    @Component('components.modules', {
        templateUrl: 'modules-installer-component-template',
        bindings: {}
    })
    class ModulesInstallerComponent extends Progressive {
        public file: File;
        public message = {
            type: false,
            content: false
        };

        constructor(private RepositoryService: RepositoryService, private $rootScope: IRootScopeService) {
            super();
        }

        getUploadPercentage() {
            let uploadPercentage = this.getProgress();
            return uploadPercentage + '%';
        }

        install() {
            this.startProcessing();
            this.RepositoryService
                .installFromFile(this.file)
                .progress((percentage) => this.setProgress(percentage))
                .then((response) => {
                    this.setMessage(response.success ? 'success' : 'error', response.data);
                    this.file = null;
                    this.$rootScope.$manager.refresh();
                })
                .catch((response) => {
                    this.setMessage('error', `(${response.status}) ${response.statusText}`);
                })
                .finally(() => {
                    this.stopProcessing();
                });
        }

        setMessage(type, content) {
            this.message.type = type;
            this.message.content = content;
        }

    }
}