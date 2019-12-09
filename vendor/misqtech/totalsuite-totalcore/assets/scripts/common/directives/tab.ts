///<reference path="../../common/decorators.ts"/>
///<reference path="../providers/tab.ts"/>
namespace TotalCore.Common.Directives {
    import TabService = TotalCore.Common.Providers.TabService;

    @Directive('directives.common', 'tabSwitch')
    export class Tabs {
        constructor(TabService: TabService) {
            return {
                restrict: 'A',
                link: function ($scope, element: any, attributes: any) {
                    if (!attributes.tabSwitch) {
                        return;
                    }

                    let parsed = TabService.parse(attributes.tabSwitch);

                    if (!parsed.name || parsed.name.trim() == "") {
                        parsed.name = Date.now().toString();
                    }

                    if (!parsed.group || parsed.group.trim() == "") {
                        parsed.group = 'default';
                        element.attr('tab-switch', `${parsed.group}>${parsed.name}`);
                    }
                    TabService.put(`${parsed.root ? parsed.root + '>' : ''}${parsed.group}>${parsed.name}`, parsed.group, parsed.name, element);

                    element.on('click', () => {
                        $scope.$applyAsync(() => TabService.set(parsed.group, parsed.name));
                        return false;
                    });
                }
            };
        }
    }
}