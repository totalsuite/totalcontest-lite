///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/tab.ts"/>
///<reference path="../helpers.ts"/>
namespace TotalCore.Common.Filters {
    import TabService = TotalCore.Common.Providers.TabService;

    @Filter('filters.common', 'isTab')
    export class Tab {
        constructor(TabService: TabService) {
            return (input: any) => {
                return TabService.is(input);
            }
        }
    }
}