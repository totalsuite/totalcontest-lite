///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Dashboard.Components {
    import Component = TotalCore.Common.Component;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-links-component-template',
        bindings: {
            heading: "<",
            description: "<",
            links: "<",
        }
    })
    class DashboardLinksComponent {

    }
}