///<reference path="../../common/decorators.ts"/>
///<reference path="../providers/settings.ts"/>
namespace TotalCore.Common.Directives {
    import IDirective = angular.IDirective;
    import IRootScopeService = angular.IRootScopeService;
    import IInterpolateService = angular.IInterpolateService;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Directive('directives.common', 'i18n')
    export class I18nDirective implements IDirective {
        constructor($rootScope: IRootScopeService, $interpolate: IInterpolateService, SettingsService: SettingsService) {
            return {
                restrict: 'EA',
                template: function (element: any, attributes: any) {
                    attributes.originalText = element.text().trim();
                    return SettingsService.i18n.expressions[attributes.originalText] || element.text();
                },
                scope: true,
                link: function link(scope, element, attributes, controller, transcludeFn) {
                    $rootScope.$on('languageChanged', function () {
                        let translation = SettingsService.i18n.expressions[attributes.originalText] || attributes.originalText;
                        translation = $interpolate(translation)(scope);
                        element.text(translation);
                    });
                }
            }
        };
    }
}