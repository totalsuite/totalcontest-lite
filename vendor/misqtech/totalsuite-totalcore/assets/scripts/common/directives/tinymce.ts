///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {
    import IDirective = angular.IDirective;
    import ISCEService = angular.ISCEService;
    import IRootScopeService = angular.IRootScopeService;
    import ITimeoutService = angular.ITimeoutService;
    import ICompileService = angular.ICompileService;

    @Directive('directives.common', 'tinymce')
    export class TinyMCE implements IDirective {
        constructor($sce: ISCEService, $rootScope: IRootScopeService, $timeout: ITimeoutService, $compile: ICompileService) {
            return {
                require: ['ngModel'],
                restrict: 'E',
                scope: {
                    'model': '=ngModel',
                },
                link: function ($scope, element: any, attributes: any, ctrls: any) {
                    var tinymceElement: JQuery;
                    var debouncedUpdate: (ed) => any;
                    var editor: any;
                    var updateView: (editor) => any;
                    var uniqueId: number;
                    var template: string;
                    var id: string;
                    var settings: any;

                    if (!window['tinymce']) {
                        return;
                    }

                    updateView = function (editor) {
                        $scope.model = editor.getContent().trim();
                        if (!$rootScope.$$phase) {
                            $scope.$digest();
                        }
                    };

                    debouncedUpdate = (function (debouncedUpdateDelay) {
                        var debouncedUpdateTimer;
                        return function (editorInstance) {
                            $timeout.cancel(debouncedUpdateTimer);
                            debouncedUpdateTimer = $timeout(function () {
                                return (function (editorInstance) {
                                    if (editorInstance.isDirty()) {
                                        editorInstance.save();
                                        updateView(editorInstance);
                                    }
                                })(editorInstance);
                            }, debouncedUpdateDelay);
                        };
                    })(400);

                    template = window['TinyMCETemplate'] || '';
                    uniqueId = Date.now() + Math.floor(Math.random() * 30);

                    id = `tinymce-field-${uniqueId}`;
                    settings = angular.copy(window['tinyMCEPreInit']['mceInit']['tinymce-field']);
                    settings.selector = `#${id}`;
                    settings.cache_suffix = `wp-mce-${uniqueId}`;
                    settings.body_class = settings.body_class.replace('tinymce-field', id);

                    settings.init_instance_callback = (editor) => {
                        editor.on('ExecCommand change NodeChange ObjectResized', function () {
                            debouncedUpdate(editor);
                        });

                        try {
                            editor.setContent($scope.model || '');
                        } catch (ex) {
                            if (!(ex instanceof TypeError)) {
                                console.error(ex);
                            }
                        }


                        if (window['switchEditors']) {
                            window['switchEditors'].go(id, 'html');
                        }
                    };

                    template = template
                        .replace(/name="tinymce\-textarea\-name"/g, `name="${id}" ng-model="model"`)
                        .replace(/tinymce\-field/g, id);


                    tinymceElement = $compile(template)($scope);
                    element.append(tinymceElement);

                    window['tinyMCEPreInit'].mceInit[id] = settings;
                    window['tinyMCEPreInit']['qtInit'][id] = angular.copy(window['tinyMCEPreInit']['qtInit']['tinymce-field']);
                    window['tinyMCEPreInit']['qtInit'][id].id = id;


                    window['tinymce'].init(settings);
                    window['quicktags']({id: id, buttons: window['tinyMCEPreInit']['qtInit'][id].buttons});
                    window['QTags']._buttonsInit();
                }
            }
        };

    }
}