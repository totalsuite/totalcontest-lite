namespace TotalCore.Dashboard.Providers {
    import Service = TotalCore.Common.Service;

    @Service('services.dashboard')
    export class RepositoryService {
        public resource;

        constructor($resource, ajaxEndpoint, prefix) {
            this.resource = $resource(ajaxEndpoint, {}, {
                activate: {method: 'GET', params: {action: `${prefix}_dashboard_activate`}},
                account: {method: 'GET', params: {action: `${prefix}_dashboard_account`}},
            });

            return this;
        }

        postAccount(account) {
            return this.resource.account(account).$promise;
        }

        postActivation(activation) {
            return this.resource.activate(activation).$promise;
        }
    }
}