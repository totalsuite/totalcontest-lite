///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Dashboard.Components {
    import Component = TotalCore.Common.Component;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-review-component-template',
        bindings: {}
    })
    class DashboardReviewComponent {
        randomTweet = '';

        constructor(public SettingsService: SettingsService) {

        }

        $onInit() {
            this.randomTweet = this.getRandomTweet();
        }

        getRandomTweet() {
            return this.SettingsService.presets['tweets'][Math.floor(Math.random() * this.SettingsService.presets['tweets'].length)];
        }
    }
}