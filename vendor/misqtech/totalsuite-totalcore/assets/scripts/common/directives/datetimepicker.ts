///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {

    @Directive('directives.common', 'datetimePicker')
    export class Datetimepicker {
        constructor() {
            return {
                restrict: 'A',
                require: 'ngModel',
                scope: {
                    'model': '=ngModel'
                },
                link: ($scope, element: any, attributes: any, ngModel: any) => {
                    let defaultOptions = {
                        format: 'm/d/Y H:i',
                        validateOnBlur: false
                    };
                    let userOptions = JSON.parse(attributes.datetimePicker || "{}");

                    let mergedOptions = angular.merge({}, defaultOptions, userOptions);
                    element.datetimepicker(mergedOptions);

                    $scope.$watch('model', (date, oldDate) => {
                        if (date != oldDate) {
                            element.val(date);
                        }
                    });
                }
            }
        }
    }
}