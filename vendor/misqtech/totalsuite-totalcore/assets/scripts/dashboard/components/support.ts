///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Dashboard.Components {
    import Component = TotalCore.Common.Component;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-support-component-template',
        bindings: {}
    })
    class DashboardSupportComponent {
        public sections = this.SettingsService.support['sections'] || [];

        constructor(public SettingsService: SettingsService) {
        }
    }
}