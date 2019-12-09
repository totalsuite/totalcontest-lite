///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Modules.Providers {
    import ISCEService = angular.ISCEService;
    import Service = TotalCore.Common.Service;

    @Service('services.modules')
    export class RepositoryService {
        public cached = {};
        public resource;

        constructor($resource, private prefix, private $sce: ISCEService, ajaxEndpoint) {
            this.resource = $resource(ajaxEndpoint, {}, {
                list: {method: 'GET', isArray: true, cache: false, params: {action: `${prefix}_modules_list`}},
                update: {method: 'POST', params: {action: `${prefix}_modules_update`}},
                uninstall: {method: 'POST', params: {action: `${prefix}_modules_uninstall`}},
                activate: {method: 'POST', params: {action: `${prefix}_modules_activate`}},
                deactivate: {method: 'POST', params: {action: `${prefix}_modules_deactivate`}},
                installFromStore: {method: 'POST', params: {action: `${prefix}_modules_install_from_store`}},
                installFromFile: {
                    method: 'POST',
                    params: {action: `${prefix}_modules_install_from_file`},
                    uploadEventHandlers: {
                        progress: (event) => {
                            var progress = Math.round(event.loaded / event.total * 100);
                            if (progress) {
                                RepositoryService.uploadProgress(progress);
                            }
                        }
                    }
                },
            });
        }

        activate(id, type) {
            return this.resource.activate({id: id, type: type}).$promise;
        }

        deactivate(id, type) {
            return this.resource.deactivate({id: id, type: type}).$promise;
        }

        installFromFile(file: File) {
            return this.progressivePromise(
                this.resource
                    .installFromFile({module: file})
                    .$promise
            );
        }

        installFromStore(id, type) {
            return this.resource.installFromStore({id: id, type: type}).$promise;
        }

        list(hardRefresh = false) {
            return this.resource
                .list({hard: Number(hardRefresh)})
                .$promise
                .then((response) => {
                    return response.map((module) => {
                        module.description = this.$sce.trustAsHtml(module.description);
                        return new TotalCore.Modules.Models.Module(module);
                    });
                });
        }

        uninstall(id, type) {
            return this.resource.update({id: id, type: type}).$promise;
        }

        update(id, type) {
            return this.resource.update({id: id, type: type}).$promise;
        }

        public static uploadProgress = angular.noop;

        private progressivePromise(promise) {
            promise.progress = function (callback) {
                if (angular.isFunction(callback)) {
                    RepositoryService.uploadProgress = callback;
                    RepositoryService.uploadProgress(1);
                }
                return this;
            };

            return promise;
        }

    }
}