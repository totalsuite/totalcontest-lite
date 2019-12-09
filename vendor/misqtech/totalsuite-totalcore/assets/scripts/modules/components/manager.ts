///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Modules.Components {
    import ILocationService = angular.ILocationService;
    import RepositoryService = TotalCore.Modules.Providers.RepositoryService;
    import Component = TotalCore.Common.Component;

    @Component('components.modules', {
        templateUrl: 'modules-manager-component-template',
        bindings: {
            'type': '<'
        }
    })
    class ModulesManagerComponent {
        public modules = null;
        public states = {};
        public type = 'templates';

        constructor(private $location: ILocationService, private $timeout, private RepositoryService: RepositoryService) {

        }

        $onInit() {
            this.refresh();
        }

        activate(module) {
            module.set('activated', true);
        }

        applyAction(action, module) {
            if (!this.states[module.getId()]) {
                this.states[module.getId()] = {};
            }

            this.states[module.getId()].processing = true;
            this.states[module.getId()].success = false;
            this.states[module.getId()].error = false;

            this.RepositoryService[action](module.getId(), module.getType())
                .then((response) => {
                    if (response.success) {
                        this.states[module.getId()].success = true;
                        this[action](module);
                    } else {
                        this.states[module.getId()].error = response.data || response;
                    }
                })
                .catch(() => {
                    this.states[module.getId()].error = 'Something went wrong! Please try again.';
                })
                .finally(() => {
                    this.$timeout(() => {
                        this.states[module.getId()].success = false;
                        this.states[module.getId()].processing = false;
                    }, 750);
                });
        }

        deactivate(module) {
            module.set('activated', false);
        }

        dismissError(module) {
            if (this.states[module.getId()]) {
                this.states[module.getId()].error = false;
            }
        }

        findModuleIndexById(id): number {
            var index: number = 0;

            for (index; index < this.modules.length; index++) {
                if (this.modules[index].id == id) {
                    return index;
                }
            }

            return null;
        }

        getError(module) {
            if (this.states[module.getId()]) {
                return this.states[module.getId()].error;
            }
        }

        getModules() {
            return this.modules === null ? [] : this.modules.filter((module) => {
                return module.getType() === this.getType();
            });
        }

        getType() {
            return this.type === 'extensions' ? 'extension' : 'template';
        }

        installFromStore(module) {
            module.set('installed', true);
        }

        isProcessing(module) {
            if (this.states[module.getId()]) {
                return Boolean(this.states[module.getId()].processing);
            }
        }

        isSuccessful(module) {
            if (this.states[module.getId()]) {
                return Boolean(this.states[module.getId()].success);
            }
        }

        refresh(hardRefresh = false) {
            this.modules = null;
            this.RepositoryService.list(hardRefresh).then((modulesList) => {
                this.modules = modulesList;
            });
        }

        uninstall(module) {
            if (module.getSource() === 'store') {
                module.set('installed', false);
                module.set('activated', false);
            } else {
                this.modules.splice(this.findModuleIndexById(module.getId()), 1);
            }
        }

        update(module) {
            module.set('version', module.getLastVersion());
            module.set('update', false);
        }

    }
}