///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {

    @Directive('directives.common', 'chart')
    export class Chart {

        constructor() {
            return {
                restrict: 'E',
                require: 'ngModel',
                scope: {
                    'model': '=ngModel'
                },
                template: '<canvas></canvas>',
                link: ($scope, element: any, attributes: any, ngModel: any) => {
                    var chartInstance;

                    if (!window['Chart']) {
                        throw new Error('Chart.js library is required for charts.');
                    }

                    let defaultOptions: any = {
                        type: attributes.type || 'line',
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }],
                                xAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            }
                        },
                        data: $scope.model || {datasets: []},
                    };

                    if (defaultOptions.type === 'doughnut') {
                        delete defaultOptions.options.scales;
                    }

                    let userOptions = JSON.parse(attributes.options || "{}");
                    let mergedOptions = angular.merge({}, defaultOptions, userOptions);

                    chartInstance = new window['Chart'](element.find('canvas'), mergedOptions);

                    $scope.$watch('model', (newValue, oldValue, scope) => {
                        if (newValue === oldValue) {
                            return;
                        }
                        chartInstance.data = angular.copy(newValue);
                        chartInstance.update();
                    }, true);
                }
            }
        }
    }
}