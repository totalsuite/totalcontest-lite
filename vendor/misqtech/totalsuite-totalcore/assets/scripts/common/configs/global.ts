///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Configs {
    import ILocationProvider = angular.ILocationProvider;
    import ICompileProvider = angular.ICompileProvider;

    @Injectable()
    export class GlobalConfig {
        constructor($locationProvider: ILocationProvider, $compileProvider: ICompileProvider) {
            $locationProvider.html5Mode({enabled: true, requireBase: false, rewriteLinks: false});
            // $compileProvider.debugInfoEnabled(false);
            // $compileProvider.commentDirectivesEnabled(false);
            // $compileProvider.cssClassDirectivesEnabled(false);
        }

    }
}