///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {
    @Directive('directives.common', 'colorPicker')
    export class Colorpicker {
        constructor() {
            return {
                restrict: 'A',
                require: 'ngModel',
                scope: {
                    'model': '=ngModel'
                },
                link: ($scope, element: any, attributes: any, ngModel: any) => {
                    let updateModel = (color) => {
                        ngModel.$setViewValue(color);
                        ngModel.$render();
                        $scope.$applyAsync();
                    };

                    let defaultOptions = {
                        change: (event, ui) => {
                            updateModel(ui.color.toCSS());
                        },
                        clear: () => {
                            updateModel('');
                        }
                    };

                    let userOptions = JSON.parse(attributes.colorPicker || "{}");
                    let mergedOptions = angular.merge({}, defaultOptions, userOptions);

                    element.wpColorPicker(mergedOptions);
                    element.wpColorPicker('color', $scope.model);

                    $scope.$watch('model', (color, oldColor) => {
                        if (color != oldColor) {
                            element.wpColorPicker('color', color);
                        }
                    });
                }
            };
        }
    }
}