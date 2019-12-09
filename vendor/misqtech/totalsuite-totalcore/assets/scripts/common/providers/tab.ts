///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Providers {
    import ILocationService = angular.ILocationService;
    import IRootScopeService = angular.IRootScopeService;

    @Service('services.common')
    export class TabService {
        public currentTab: any = '';
        private tabs: any = {};

        public constructor(private $location: ILocationService, public $rootScope: IRootScopeService) {
            let urlParams = this.$location.search();
            $rootScope.isCurrentTab = (tab) => {
                return this.is(tab);
            };
            $rootScope.setCurrentTab = (tab) => {
                let parsed = this.parse(tab);
                return this.set(parsed.group, parsed.name);
            };
            $rootScope.getCurrentTab = () => {
                return this.currentTab;
            };

            let tabs = (urlParams.tab || '')['split']('>');
            for (let index = 0; index < tabs.length; index = index + 2) {
                let group = tabs[index + 1] ? tabs[index] : tabs[index - 1];
                let tab = tabs[index + 1] || tabs[index];
                $rootScope.$applyAsync(() => {
                    this.set(group, tab);
                });
            }
        }

        get(group: string, name: string): JQuery {
            return this.tabs[group][name] || false;
        }

        is(tabName) {
            return this.currentTab.indexOf(tabName) !== -1;
        }

        parse(tab): { group, name, root } {
            let composedName: string[];
            let name: string;
            let group: string;

            composedName = tab.split('>');
            name = composedName.pop();
            group = composedName.pop();

            return {group: group, name: name, root: composedName.join('>')};
        }

        put(fullName: string, group: string, name: string, element: JQuery): void {
            this.tabs[group] = this.tabs[group] || {};
            this.tabs[group][name] = {
                element: element,
                fullName: fullName
            };
        }

        set(group: string, name: string) {
            if (!this.tabs[group] || !this.tabs[group][name]) {
                return;
            }
            angular.forEach(this.tabs[group], (tab, key) => {
                angular.element(document).find(`[tab="${tab.fullName}"]`).removeClass('active');
                tab.element.removeClass('active');
            });
            this.tabs[group][name].element.addClass('active');
            this.currentTab = this.tabs[group][name].fullName;
            angular.element(document).find(`[tab="${this.currentTab}"]`).addClass('active');
            this.$location.search('tab', this.currentTab);
        }
    }
}