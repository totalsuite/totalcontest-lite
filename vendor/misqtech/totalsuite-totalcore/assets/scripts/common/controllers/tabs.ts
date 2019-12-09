///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Controllers {
    import ILocationService = angular.ILocationService;

    @Controller('controllers.common')
    export abstract class TabsController {
        protected tab;

        constructor(private $location: ILocationService) {
            let urlParams = this.$location.search();
            this.setTab(urlParams.tab || this.getDefaultTab());
        }

        abstract getDefaultTab();

        getTab() {
            return this.tab;
        }

        isTab(tab) {
            return tab === this.tab;
        }

        setTab(tab) {
            this.tab = tab;
            this.updateURL();
        }

        updateURL() {
            this.$location.search('tab', this.tab);
        }
    }

}