///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Dashboard.Components {
    import Component = TotalCore.Common.Component;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-whats-new-component-template',
        bindings: {}
    })
    class DashboardWhatsNewComponent {
        public versions = this.SettingsService.versions || [];

        constructor(public SettingsService: SettingsService) {

        }
    }
}