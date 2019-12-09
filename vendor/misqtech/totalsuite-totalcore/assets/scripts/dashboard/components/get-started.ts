///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Dashboard.Components {
    import ISCEService = angular.ISCEService;
    import Component = TotalCore.Common.Component;

    @Component('components.dashboard', {
        templateUrl: 'dashboard-get-started-component-template',
        bindings: {}
    })
    class DashboardGetStartedComponent {
        public videoId;

        constructor(private $sce: ISCEService) {

        }

        getEmbedUrl() {
            return this.$sce.trustAsResourceUrl(`https://www.youtube-nocookie.com/embed/${this.videoId}?rel=0&amp;showinfo=0`);
        }

        isPlayingVideo(videoId) {
            return this.videoId === videoId;
        }

        playVideo(videoId) {
            this.videoId = videoId;
        }
    }
}