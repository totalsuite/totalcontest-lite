var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
/**
 * Decorators.
 */
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        /**
         * A small helper to inject dependencies dynamically.
         *
         * @param func
         */
        function annotate(func) {
            var $injector = angular.injector(['ng']);
            func.$inject = $injector.annotate(func).map(function (member) { return member.replace(/^_/, ''); });
        }
        /**
         * Injectable decorator.
         *
         * @returns {(Entity: any) => void}
         * @constructor
         */
        function Injectable() {
            return function (Entity) {
                annotate(Entity);
            };
        }
        Common.Injectable = Injectable;
        /**
         * Service decorator.
         *
         * @param {string} moduleName
         * @returns {(Service: any) => void}
         * @constructor
         */
        function Service(moduleName) {
            return function (Service) {
                var module;
                var name = Service.name;
                var isProvider = Service.hasOwnProperty('$get');
                annotate(Service);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module[isProvider ? 'provider' : 'service'](name, Service);
            };
        }
        Common.Service = Service;
        /**
         * Factory decorator.
         *
         * @param {string} moduleName
         * @param selector
         * @returns {(Factory: any) => void}
         * @constructor
         */
        function Factory(moduleName, selector) {
            return function (Factory) {
                var module;
                var name = selector || ("" + Factory.name.charAt(0).toLowerCase() + Factory.name.slice(1)).replace('Factory', '');
                annotate(Factory);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module.factory(name, Factory);
            };
        }
        Common.Factory = Factory;
        /**
         * Controller decorator.
         *
         * @param {string} moduleName
         * @returns {(Controller: any) => void}
         * @constructor
         */
        function Controller(moduleName) {
            return function (Controller) {
                var module;
                var name = Controller.name;
                annotate(Controller);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module.controller(name, Controller);
            };
        }
        Common.Controller = Controller;
        /**
         * Filter decorator.
         *
         * @param {string} moduleName
         * @param selector
         * @returns {(Filter: any) => void}
         * @constructor
         */
        function Filter(moduleName, selector) {
            return function (Filter) {
                var module;
                var name = selector || ("" + Filter.name.charAt(0).toLowerCase() + Filter.name.slice(1)).replace('Filter', '');
                annotate(Filter);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module.filter(name, Filter);
            };
        }
        Common.Filter = Filter;
        /**
         * Component decorator.
         *
         * @param moduleName
         * @param {angular.IComponentOptions} options
         * @param {any} selector
         * @returns {(Class: any) => void}
         * @constructor
         */
        function Component(moduleName, options, selector) {
            if (selector === void 0) { selector = null; }
            return function (Class) {
                var module;
                selector = selector || ("" + Class.name.charAt(0).toLowerCase() + Class.name.slice(1)).replace('Component', '');
                options.controller = Class;
                annotate(Class);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module.component(selector, options);
            };
        }
        Common.Component = Component;
        /**
         * Directive decorator.
         *
         * @param moduleName
         * @param {any} selector
         * @returns {(Class: any) => void}
         * @constructor
         */
        function Directive(moduleName, selector) {
            if (selector === void 0) { selector = null; }
            return function (Class) {
                var module;
                selector = selector || ("" + Class.name.charAt(0).toLowerCase() + Class.name.slice(1)).replace('Directive', '');
                annotate(Class);
                try {
                    module = angular.module(moduleName);
                }
                catch (exception) {
                    module = angular.module(moduleName, []);
                }
                module.directive(selector, Class);
            };
        }
        Common.Directive = Directive;
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Configs;
        (function (Configs) {
            var GlobalConfig = /** @class */ (function () {
                function GlobalConfig($locationProvider, $compileProvider) {
                    $locationProvider.html5Mode({ enabled: true, requireBase: false, rewriteLinks: false });
                    // $compileProvider.debugInfoEnabled(false);
                    // $compileProvider.commentDirectivesEnabled(false);
                    // $compileProvider.cssClassDirectivesEnabled(false);
                }
                GlobalConfig = __decorate([
                    Common.Injectable()
                ], GlobalConfig);
                return GlobalConfig;
            }());
            Configs.GlobalConfig = GlobalConfig;
        })(Configs = Common.Configs || (Common.Configs = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Configs;
        (function (Configs) {
            var HttpConfig = /** @class */ (function () {
                function HttpConfig($resourceProvider, $httpProvider, $compileProvider) {
                    // Don't strip trailing slashes from calculated URLs
                    $resourceProvider.defaults.stripTrailingSlashes = false;
                    $httpProvider.defaults.transformRequest = function (data) {
                        if (data === undefined) {
                            return data;
                        }
                        return HttpConfig_1.serializer(new FormData(), data);
                    };
                    $httpProvider.defaults.headers.post['Content-Type'] = undefined;
                    $compileProvider.debugInfoEnabled(false);
                }
                HttpConfig_1 = HttpConfig;
                HttpConfig.serializer = function (form, fields, parent) {
                    angular.forEach(fields, function (fieldValue, fieldName) {
                        if (parent) {
                            fieldName = parent + "[" + fieldName + "]";
                        }
                        if (fieldValue !== null && typeof fieldValue === 'object' && (fieldValue.__proto__ === Object.prototype || fieldValue.__proto__ === Array.prototype)) {
                            HttpConfig_1.serializer(form, fieldValue, fieldName);
                        }
                        else {
                            if (typeof fieldValue === 'boolean') {
                                fieldValue = Number(fieldValue);
                            }
                            else if (fieldValue === null) {
                                fieldValue = '';
                            }
                            form.append(fieldName, fieldValue);
                        }
                    });
                    return form;
                };
                HttpConfig = HttpConfig_1 = __decorate([
                    Common.Injectable()
                ], HttpConfig);
                return HttpConfig;
                var HttpConfig_1;
            }());
            Configs.HttpConfig = HttpConfig;
        })(Configs = Common.Configs || (Common.Configs = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Providers;
        (function (Providers) {
            var SettingsService = /** @class */ (function () {
                function SettingsService(namespace, prefix) {
                    this.namespace = namespace;
                    this.prefix = prefix;
                    this.account = window[this.namespace + "Account"] || [];
                    this.activation = window[this.namespace + "Activation"] || [];
                    this.defaults = window[this.namespace + "Defaults"] || {};
                    this.i18n = window[this.namespace + "I18n"] || [];
                    this.information = window[this.namespace + "Information"] || {};
                    this.languages = window[this.namespace + "Languages"] || [];
                    this.modules = window[this.namespace + "Modules"] || {};
                    this.presets = window[this.namespace + "Presets"] || [];
                    this.settings = window[this.namespace + "Settings"] || {};
                    this.support = window[this.namespace + "Support"] || [];
                    this.templates = window[this.namespace + "Templates"] || {};
                    this.versions = window[this.namespace + "Versions"] || [];
                    this.settings['id'] = this.defaults['id'];
                    this.settings = angular.merge({}, this.defaults, this.settings);
                }
                SettingsService = __decorate([
                    Common.Service('services.common')
                ], SettingsService);
                return SettingsService;
            }());
            Providers.SettingsService = SettingsService;
        })(Providers = Common.Providers || (Common.Providers = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Providers;
        (function (Providers) {
            var TabService = /** @class */ (function () {
                function TabService($location, $rootScope) {
                    var _this = this;
                    this.$location = $location;
                    this.$rootScope = $rootScope;
                    this.currentTab = '';
                    this.tabs = {};
                    var urlParams = this.$location.search();
                    $rootScope.isCurrentTab = function (tab) {
                        return _this.is(tab);
                    };
                    $rootScope.setCurrentTab = function (tab) {
                        var parsed = _this.parse(tab);
                        return _this.set(parsed.group, parsed.name);
                    };
                    $rootScope.getCurrentTab = function () {
                        return _this.currentTab;
                    };
                    var tabs = (urlParams.tab || '')['split']('>');
                    var _loop_1 = function (index) {
                        var group = tabs[index + 1] ? tabs[index] : tabs[index - 1];
                        var tab = tabs[index + 1] || tabs[index];
                        $rootScope.$applyAsync(function () {
                            _this.set(group, tab);
                        });
                    };
                    for (var index = 0; index < tabs.length; index = index + 2) {
                        _loop_1(index);
                    }
                }
                TabService.prototype.get = function (group, name) {
                    return this.tabs[group][name] || false;
                };
                TabService.prototype.is = function (tabName) {
                    return this.currentTab.indexOf(tabName) !== -1;
                };
                TabService.prototype.parse = function (tab) {
                    var composedName;
                    var name;
                    var group;
                    composedName = tab.split('>');
                    name = composedName.pop();
                    group = composedName.pop();
                    return { group: group, name: name, root: composedName.join('>') };
                };
                TabService.prototype.put = function (fullName, group, name, element) {
                    this.tabs[group] = this.tabs[group] || {};
                    this.tabs[group][name] = {
                        element: element,
                        fullName: fullName
                    };
                };
                TabService.prototype.set = function (group, name) {
                    if (!this.tabs[group] || !this.tabs[group][name]) {
                        return;
                    }
                    angular.forEach(this.tabs[group], function (tab, key) {
                        angular.element(document).find("[tab=\"" + tab.fullName + "\"]").removeClass('active');
                        tab.element.removeClass('active');
                    });
                    this.tabs[group][name].element.addClass('active');
                    this.currentTab = this.tabs[group][name].fullName;
                    angular.element(document).find("[tab=\"" + this.currentTab + "\"]").addClass('active');
                    this.$location.search('tab', this.currentTab);
                };
                TabService = __decorate([
                    Common.Service('services.common')
                ], TabService);
                return TabService;
            }());
            Providers.TabService = TabService;
        })(Providers = Common.Providers || (Common.Providers = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
///<reference path="../providers/tab.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Directives;
        (function (Directives) {
            var Tabs = /** @class */ (function () {
                function Tabs(TabService) {
                    return {
                        restrict: 'A',
                        link: function ($scope, element, attributes) {
                            if (!attributes.tabSwitch) {
                                return;
                            }
                            var parsed = TabService.parse(attributes.tabSwitch);
                            if (!parsed.name || parsed.name.trim() == "") {
                                parsed.name = Date.now().toString();
                            }
                            if (!parsed.group || parsed.group.trim() == "") {
                                parsed.group = 'default';
                                element.attr('tab-switch', parsed.group + ">" + parsed.name);
                            }
                            TabService.put("" + (parsed.root ? parsed.root + '>' : '') + parsed.group + ">" + parsed.name, parsed.group, parsed.name, element);
                            element.on('click', function () {
                                $scope.$applyAsync(function () { return TabService.set(parsed.group, parsed.name); });
                                return false;
                            });
                        }
                    };
                }
                Tabs = __decorate([
                    Common.Directive('directives.common', 'tabSwitch')
                ], Tabs);
                return Tabs;
            }());
            Directives.Tabs = Tabs;
        })(Directives = Common.Directives || (Common.Directives = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Directives;
        (function (Directives) {
            var Datetimepicker = /** @class */ (function () {
                function Datetimepicker() {
                    return {
                        restrict: 'A',
                        require: 'ngModel',
                        scope: {
                            'model': '=ngModel'
                        },
                        link: function ($scope, element, attributes, ngModel) {
                            var defaultOptions = {
                                format: 'm/d/Y H:i',
                                validateOnBlur: false
                            };
                            var userOptions = JSON.parse(attributes.datetimePicker || "{}");
                            var mergedOptions = angular.merge({}, defaultOptions, userOptions);
                            element.datetimepicker(mergedOptions);
                            $scope.$watch('model', function (date, oldDate) {
                                if (date != oldDate) {
                                    element.val(date);
                                }
                            });
                        }
                    };
                }
                Datetimepicker = __decorate([
                    Common.Directive('directives.common', 'datetimePicker')
                ], Datetimepicker);
                return Datetimepicker;
            }());
            Directives.Datetimepicker = Datetimepicker;
        })(Directives = Common.Directives || (Common.Directives = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Directives;
        (function (Directives) {
            var Colorpicker = /** @class */ (function () {
                function Colorpicker() {
                    return {
                        restrict: 'A',
                        require: 'ngModel',
                        scope: {
                            'model': '=ngModel'
                        },
                        link: function ($scope, element, attributes, ngModel) {
                            var updateModel = function (color) {
                                ngModel.$setViewValue(color);
                                ngModel.$render();
                                $scope.$applyAsync();
                            };
                            var defaultOptions = {
                                change: function (event, ui) {
                                    updateModel(ui.color.toCSS());
                                },
                                clear: function () {
                                    updateModel('');
                                }
                            };
                            var userOptions = JSON.parse(attributes.colorPicker || "{}");
                            var mergedOptions = angular.merge({}, defaultOptions, userOptions);
                            element.wpColorPicker(mergedOptions);
                            element.wpColorPicker('color', $scope.model);
                            $scope.$watch('model', function (color, oldColor) {
                                if (color != oldColor) {
                                    element.wpColorPicker('color', color);
                                }
                            });
                        }
                    };
                }
                Colorpicker = __decorate([
                    Common.Directive('directives.common', 'colorPicker')
                ], Colorpicker);
                return Colorpicker;
            }());
            Directives.Colorpicker = Colorpicker;
        })(Directives = Common.Directives || (Common.Directives = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Directives;
        (function (Directives) {
            var TinyMCE = /** @class */ (function () {
                function TinyMCE($sce, $rootScope, $timeout, $compile) {
                    return {
                        require: ['ngModel'],
                        restrict: 'E',
                        scope: {
                            'model': '=ngModel',
                        },
                        link: function ($scope, element, attributes, ctrls) {
                            var tinymceElement;
                            var debouncedUpdate;
                            var editor;
                            var updateView;
                            var uniqueId;
                            var template;
                            var id;
                            var settings;
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
                            id = "tinymce-field-" + uniqueId;
                            settings = angular.copy(window['tinyMCEPreInit']['mceInit']['tinymce-field']);
                            settings.selector = "#" + id;
                            settings.cache_suffix = "wp-mce-" + uniqueId;
                            settings.body_class = settings.body_class.replace('tinymce-field', id);
                            settings.init_instance_callback = function (editor) {
                                editor.on('ExecCommand change NodeChange ObjectResized', function () {
                                    debouncedUpdate(editor);
                                });
                                try {
                                    editor.setContent($scope.model || '');
                                }
                                catch (ex) {
                                    if (!(ex instanceof TypeError)) {
                                        console.error(ex);
                                    }
                                }
                                if (window['switchEditors']) {
                                    window['switchEditors'].go(id, 'html');
                                }
                            };
                            template = template
                                .replace(/name="tinymce\-textarea\-name"/g, "name=\"" + id + "\" ng-model=\"model\"")
                                .replace(/tinymce\-field/g, id);
                            tinymceElement = $compile(template)($scope);
                            element.append(tinymceElement);
                            window['tinyMCEPreInit'].mceInit[id] = settings;
                            window['tinyMCEPreInit']['qtInit'][id] = angular.copy(window['tinyMCEPreInit']['qtInit']['tinymce-field']);
                            window['tinyMCEPreInit']['qtInit'][id].id = id;
                            window['tinymce'].init(settings);
                            window['quicktags']({ id: id, buttons: window['tinyMCEPreInit']['qtInit'][id].buttons });
                            window['QTags']._buttonsInit();
                        }
                    };
                }
                ;
                TinyMCE = __decorate([
                    Common.Directive('directives.common', 'tinymce')
                ], TinyMCE);
                return TinyMCE;
            }());
            Directives.TinyMCE = TinyMCE;
        })(Directives = Common.Directives || (Common.Directives = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Directives;
        (function (Directives) {
            var CopyToClipboard = /** @class */ (function () {
                function CopyToClipboard() {
                    var copyToClipboard = function (text) {
                        if (window['clipboardData'] && window['clipboardData'].setData) {
                            return window['clipboardData'].setData("Text", text);
                        }
                        else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                            var textarea = document.createElement("textarea");
                            textarea.textContent = text;
                            textarea.style.position = "fixed";
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                return document.execCommand("copy");
                            }
                            catch (ex) {
                                prompt('', text);
                                return false;
                            }
                            finally {
                                document.body.removeChild(textarea);
                            }
                        }
                    };
                    return {
                        restrict: 'A',
                        link: function ($scope, element, attributes) {
                            element.on('click', function () {
                                var originalHTML = element.html();
                                copyToClipboard(attributes['copyToClipboard']);
                                element.html(attributes['copyToClipboardDone'] || '<span class="dashicons dashicons-yes"></span>');
                                setTimeout(function () {
                                    element.html(originalHTML);
                                }, 1000);
                            });
                        }
                    };
                }
                CopyToClipboard = __decorate([
                    Common.Directive('directives.common', 'copyToClipboard')
                ], CopyToClipboard);
                return CopyToClipboard;
            }());
            Directives.CopyToClipboard = CopyToClipboard;
        })(Directives = Common.Directives || (Common.Directives = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Filters;
        (function (Filters) {
            var Slug = /** @class */ (function () {
                function Slug() {
                    return function (input, separator) {
                        if (separator === void 0) { separator = '-'; }
                        return Slug_1.filter(input, separator);
                    };
                }
                Slug_1 = Slug;
                Slug.filter = function (name, separator, keepEdge) {
                    if (keepEdge === void 0) { keepEdge = false; }
                    if (name && name.toString) {
                        return name
                            .toString()
                            .toLowerCase()
                            .replace(/\s+/g, separator) // Replace spaces with -
                            .replace(new RegExp(("[\\" + Slug_1.specialChars.join('\\') + "]+").replace("\\" + separator, ''), 'g'), '') // Remove all non-word chars
                            .replace(new RegExp("\\" + separator + "\\" + separator + "+", 'g'), separator) // Replace multiple - with single -
                            .replace(keepEdge ? null : new RegExp("^\\" + separator + "+"), '') // Trim - from start of text
                            .replace(keepEdge ? null : new RegExp("\\" + separator + "+$"), ''); // Trim - from end of text
                    }
                };
                Slug.$inject = [];
                Slug.specialChars = ["!", "@", "#", "$", "%", "^", "&", "*", "<", ">", ":", ".", ";", ",", "!", "?", "§", "¨", "£", "-", "_", "(", ")", "{", "}", "[", "]", "=", "+", "|", "~", "`", "'", "°", '"', "/", "\\"];
                Slug = Slug_1 = __decorate([
                    Common.Filter('filters.common', 'slug')
                ], Slug);
                return Slug;
                var Slug_1;
            }());
            Filters.Slug = Slug;
        })(Filters = Common.Filters || (Common.Filters = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../common/decorators.ts"/>
///<reference path="../providers/settings.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Filters;
        (function (Filters) {
            var I18n = /** @class */ (function () {
                function I18n(SettingsService) {
                    return function (input, separator) {
                        if (separator === void 0) { separator = '-'; }
                        return I18n_1.filter(input, separator, SettingsService.i18n);
                    };
                }
                I18n_1 = I18n;
                I18n.filter = function (name, separator, expressions) {
                    if (name && name.toString) {
                        return expressions[name] || name;
                    }
                };
                I18n = I18n_1 = __decorate([
                    Common.Filter('filters.common', 'i18n')
                ], I18n);
                return I18n;
                var I18n_1;
            }());
            Filters.I18n = I18n;
        })(Filters = Common.Filters || (Common.Filters = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
var TotalCore;
(function (TotalCore) {
    var Customizer;
    (function (Customizer) {
        var Components;
        (function (Components) {
            var Component = TotalCore.Common.Component;
            var CustomizerComponent = /** @class */ (function () {
                function CustomizerComponent($scope, $compile, $templateCache, $http, $sce, $element, $q, SettingsService) {
                    this.$scope = $scope;
                    this.$compile = $compile;
                    this.$templateCache = $templateCache;
                    this.$http = $http;
                    this.$sce = $sce;
                    this.$element = $element;
                    this.$q = $q;
                    this.SettingsService = SettingsService;
                    this.device = 'laptop';
                    this.devices = {
                        smartphone: {
                            width: 431,
                            height: 877,
                            canvas: {
                                width: 375,
                                height: 667,
                                top: 105,
                                right: 402,
                                bottom: 772,
                                left: 27,
                            }
                        },
                        tablet: {
                            width: 875,
                            height: 1253,
                            canvas: {
                                width: 768,
                                height: 1024,
                                top: 114,
                                right: 820,
                                bottom: 1138,
                                left: 52,
                            }
                        },
                    };
                    this.preview = {
                        screen: '',
                    };
                    this.settings = {};
                    this.tab = [];
                    this.settings = SettingsService.settings.design || this.settings;
                    this.iframe = $element.find('iframe').contents();
                    if (!this.SettingsService.templates[this.getTemplate()]) {
                        this.setTemplate(this.SettingsService.defaults.design.template);
                    }
                    window['jQuery'](window).resize(function () {
                        $scope.$applyAsync();
                    });
                }
                CustomizerComponent.prototype.$onInit = function () {
                    this.preparePreview();
                };
                CustomizerComponent.prototype.changeTemplateTo = function (template, $event) {
                    $event.stopPropagation();
                    this.setTemplate(template.id);
                    this.popToRoot();
                    this.preparePreview();
                };
                CustomizerComponent.prototype.escape = function (content) {
                    return this.$sce.trustAsHtml(content);
                };
                CustomizerComponent.prototype.getActiveTab = function () {
                    return this.tab[this.tab.length - 1];
                };
                CustomizerComponent.prototype.getActiveTabBreadcrumb = function () {
                    return this.tab
                        .map(function (tab) {
                        return tab.label;
                    })
                        .join(' / ');
                };
                CustomizerComponent.prototype.getCurrentTemplate = function (field) {
                    var template = this.SettingsService.templates[this.getTemplate()];
                    return field ? template[field] : template;
                };
                CustomizerComponent.prototype.getCurrentTemplateDefaults = function () {
                    if (this.getCurrentTemplate('defaults')) {
                        return this.$http.get(this.prefixUrl(this.getCurrentTemplate('defaults')), { cache: true }).then(function (response) { return response.data; });
                    }
                    return this.$q.resolve({});
                };
                CustomizerComponent.prototype.getCurrentTemplatePreviewContentId = function () {
                    return this.getCurrentTemplate('preview') ? this.prefixUrl(this.getCurrentTemplate('preview')) : null;
                };
                CustomizerComponent.prototype.getCurrentTemplatePreviewCssId = function () {
                    return this.getCurrentTemplate('stylesheet') ? this.prefixUrl(this.getCurrentTemplate('stylesheet')) : null;
                };
                CustomizerComponent.prototype.getCurrentTemplateSettingsId = function () {
                    return this.getCurrentTemplate('settings') ? this.prefixUrl(this.getCurrentTemplate('settings')) : null;
                };
                CustomizerComponent.prototype.getDevice = function () {
                    return this.device;
                };
                CustomizerComponent.prototype.getDeviceScaleAttributes = function () {
                    if (this.device === 'laptop') {
                        return {};
                    }
                    var scale = this.$element.find('iframe').parent().outerHeight() / this.devices[this.device].height;
                    return {
                        transform: "scale(" + scale + ")",
                        marginTop: this.devices[this.device].canvas.top * scale,
                    };
                };
                CustomizerComponent.prototype.getScreen = function () {
                    return this.preview.screen;
                };
                CustomizerComponent.prototype.getTemplate = function () {
                    return this.settings.template;
                };
                CustomizerComponent.prototype.getTemplates = function () {
                    return this.SettingsService.templates;
                };
                CustomizerComponent.prototype.hasActiveTab = function (tab) {
                    if (tab) {
                        for (var _i = 0, _a = this.tab; _i < _a.length; _i++) {
                            var item = _a[_i];
                            if (item.id === tab) {
                                return true;
                            }
                        }
                        return false;
                    }
                    else {
                        return this.tab.length > 0;
                    }
                };
                CustomizerComponent.prototype.hasActiveTabAfter = function (tab) {
                    return this.hasActiveTab(tab) && this.getActiveTab().id !== tab;
                };
                CustomizerComponent.prototype.isActiveTab = function (tab) {
                    if (this.tab.length > 0) {
                        return this.tab[this.tab.length - 1].id === tab;
                    }
                    return false;
                };
                CustomizerComponent.prototype.isDevice = function (device) {
                    return this.device === device;
                };
                CustomizerComponent.prototype.isScreen = function (screen) {
                    return this.preview.screen === screen;
                };
                CustomizerComponent.prototype.isTemplate = function (template) {
                    return this.settings.template === template;
                };
                CustomizerComponent.prototype.popActiveTab = function () {
                    this.tab.pop();
                };
                CustomizerComponent.prototype.popToRoot = function () {
                    this.tab = [];
                };
                CustomizerComponent.prototype.preparePreview = function () {
                    var _this = this;
                    var headTemplate = this.$templateCache.get('customizer-preview-head-template');
                    var bodyTemplate = this.$templateCache.get('customizer-preview-body-template');
                    var compiledHeadTemplate = this.$compile(headTemplate);
                    var compiledBodyTemplate = this.$compile(bodyTemplate);
                    this.getCurrentTemplateDefaults()
                        .then(function (defaults) {
                        _this.settings.custom = angular.extend({}, defaults, _this.settings.custom);
                        _this.$scope.custom = _this.settings.custom;
                        angular.forEach(_this.settings, function (value, key) {
                            _this.$scope[key] = value;
                        });
                    });
                    this.iframe.html('');
                    this.iframe.find('head').append(compiledHeadTemplate(this.$scope));
                    this.iframe.find('body').html(compiledBodyTemplate(this.$scope));
                };
                CustomizerComponent.prototype.resetActiveTab = function () {
                    this.tab = [];
                };
                CustomizerComponent.prototype.resetToDefaults = function (confirmBefore) {
                    var _this = this;
                    if (confirmBefore === void 0) { confirmBefore = false; }
                    if (confirmBefore && !confirm('Are you sure?')) {
                        return;
                    }
                    this.getCurrentTemplateDefaults()
                        .then(function (defaults) {
                        _this.settings.custom = angular.extend({}, _this.settings.custom, defaults);
                        _this.$scope.custom = _this.settings.custom;
                    });
                    if (confirmBefore) {
                        alert('Done');
                    }
                };
                CustomizerComponent.prototype.setActiveTab = function (tab, label) {
                    this.tab.push({ id: tab, label: label });
                };
                CustomizerComponent.prototype.setDevice = function (device) {
                    this.device = device;
                };
                CustomizerComponent.prototype.setScreen = function (screen) {
                    this.preview.screen = screen;
                };
                CustomizerComponent.prototype.setTemplate = function (template) {
                    this.settings.template = template;
                    this.resetToDefaults();
                };
                CustomizerComponent.prototype.prefixUrl = function (url) {
                    if (url.match(/^(https?:)?\/\//g)) {
                        return url;
                    }
                    return "" + this.getCurrentTemplate('url') + url;
                };
                CustomizerComponent = __decorate([
                    Component('components.customizer', {
                        templateUrl: 'customizer-component-template',
                        bindings: {}
                    })
                ], CustomizerComponent);
                return CustomizerComponent;
            }());
        })(Components = Customizer.Components || (Customizer.Components = {}));
    })(Customizer = TotalCore.Customizer || (TotalCore.Customizer = {}));
})(TotalCore || (TotalCore = {}));
var TotalCore;
(function (TotalCore) {
    var Customizer;
    (function (Customizer) {
        var Components;
        (function (Components) {
            var Component = TotalCore.Common.Component;
            var CustomizerControl = /** @class */ (function () {
                function CustomizerControl() {
                    this.type = 'text';
                }
                CustomizerControl.prototype.getTemplate = function () {
                    return "customizer-control-" + this.type + "-template";
                };
                CustomizerControl = __decorate([
                    Component('components.customizer', {
                        templateUrl: 'customizer-control-component-template',
                        bindings: {
                            type: '@',
                            label: '@',
                            help: '@',
                            options: '<',
                            ngModel: '=',
                        },
                        transclude: true,
                    })
                ], CustomizerControl);
                return CustomizerControl;
            }());
        })(Components = Customizer.Components || (Customizer.Components = {}));
    })(Customizer = TotalCore.Customizer || (TotalCore.Customizer = {}));
})(TotalCore || (TotalCore = {}));
var TotalCore;
(function (TotalCore) {
    var Customizer;
    (function (Customizer) {
        var Components;
        (function (Components) {
            var Component = TotalCore.Common.Component;
            var CustomizerTabs = /** @class */ (function () {
                function CustomizerTabs() {
                }
                CustomizerTabs.prototype.getTarget = function () {
                    return this.$content ? this.$content.getTarget() : null;
                };
                CustomizerTabs = __decorate([
                    Component('components.customizer', {
                        templateUrl: 'customizer-tabs-component-template',
                        bindings: {
                            target: '@',
                        },
                        require: {
                            $customizer: '^customizer',
                            $content: '?^^customizerTabContent',
                        },
                        transclude: true,
                    })
                ], CustomizerTabs);
                return CustomizerTabs;
            }());
        })(Components = Customizer.Components || (Customizer.Components = {}));
    })(Customizer = TotalCore.Customizer || (TotalCore.Customizer = {}));
})(TotalCore || (TotalCore = {}));
var TotalCore;
(function (TotalCore) {
    var Customizer;
    (function (Customizer) {
        var Components;
        (function (Components) {
            var Component = TotalCore.Common.Component;
            var CustomizerTab = /** @class */ (function () {
                function CustomizerTab() {
                }
                CustomizerTab.prototype.getTarget = function () {
                    return [this.$content ? this.$content.getTarget() : null, this.target].filter(Boolean).join('.');
                };
                CustomizerTab = __decorate([
                    Component('components.customizer', {
                        templateUrl: 'customizer-tab-component-template',
                        bindings: {
                            target: '@',
                        },
                        require: {
                            $customizer: '^customizer',
                            $content: '?^^customizerTabContent',
                        },
                        transclude: true,
                    })
                ], CustomizerTab);
                return CustomizerTab;
            }());
        })(Components = Customizer.Components || (Customizer.Components = {}));
    })(Customizer = TotalCore.Customizer || (TotalCore.Customizer = {}));
})(TotalCore || (TotalCore = {}));
var TotalCore;
(function (TotalCore) {
    var Customizer;
    (function (Customizer) {
        var Components;
        (function (Components) {
            var Component = TotalCore.Common.Component;
            var CustomizerTabContent = /** @class */ (function () {
                function CustomizerTabContent() {
                }
                CustomizerTabContent.prototype.getTarget = function () {
                    return [this.$content ? this.$content.getTarget() : null, this.name].filter(Boolean).join('.');
                };
                CustomizerTabContent = __decorate([
                    Component('components.customizer', {
                        templateUrl: 'customizer-tab-content-component-template',
                        bindings: {
                            name: '@',
                        },
                        require: {
                            $customizer: '^customizer',
                            $content: '?^^customizerTabContent',
                        },
                        transclude: true,
                    })
                ], CustomizerTabContent);
                return CustomizerTabContent;
            }());
        })(Components = Customizer.Components || (Customizer.Components = {}));
    })(Customizer = TotalCore.Customizer || (TotalCore.Customizer = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var EditorCtrl = /** @class */ (function () {
        function EditorCtrl($rootScope, SettingsService, $location, TabService) {
            this.$rootScope = $rootScope;
            this.SettingsService = SettingsService;
            this.$location = $location;
            this.TabService = TabService;
            this.information = this.SettingsService.information;
            this.modules = this.SettingsService.modules;
            this.settings = this.SettingsService.settings;
            var urlParams = this.$location.search();
            $rootScope.settings = this.settings;
            $rootScope.information = this.information;
            $rootScope.modules = this.modules;
            $rootScope.$applyAsync(function () {
                if (!urlParams.tab) {
                    TabService.set('editor', 'form');
                }
            });
        }
        EditorCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], EditorCtrl);
        return EditorCtrl;
    }());
    TotalContest.EditorCtrl = EditorCtrl;
})(TotalContest || (TotalContest = {}));
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var PreviewCtrl = /** @class */ (function () {
        function PreviewCtrl($scope, $sce, SettingsService) {
            this.$scope = $scope;
            this.$sce = $sce;
            this.SettingsService = SettingsService;
            this.preview = {
                screen: 'participate',
                options: {},
                submissions: [],
                layout: 'grid'
            };
            this.settings = this.SettingsService.settings;
        }
        PreviewCtrl.prototype.escape = function (content) {
            return this.$sce.trustAsHtml(content);
        };
        PreviewCtrl.prototype.generateSubmissions = function (number) {
            this.preview.submissions = [];
            for (var index = 0; index < number; index++) {
                var submission = {
                    id: index + 1,
                    date: new Date().toDateString(),
                    views: this.randomNumber(1, 1999),
                    votes: this.randomNumber(1, 1999),
                    rate: this.randomNumber(1, 5),
                    title: this.settings.contest.submissions.title,
                    subtitle: this.settings.contest.submissions.subtitle,
                };
                for (var fragment in submission) {
                    submission.title = submission.title.replace("{{" + fragment + "}}", submission[fragment]);
                    submission.subtitle = submission.subtitle.replace("{{" + fragment + "}}", submission[fragment]);
                }
                this.preview.submissions.push(submission);
            }
        };
        PreviewCtrl.prototype.getFieldClass = function (field) {
            return ['image', 'video', 'audio'].indexOf(field.type) == -1 ? "totalcontest-form-field-type-" + field.type : "totalcontest-form-field-type-file totalcontest-form-field-type-media totalcontest-form-field-type-" + field.type;
        };
        PreviewCtrl.prototype.getFieldType = function (field) {
            return ['image', 'video', 'audio'].indexOf(field.type) == -1 ? field.type : 'file';
        };
        PreviewCtrl.prototype.getLayout = function () {
            return this.preview.layout;
        };
        PreviewCtrl.prototype.getOptionsOf = function (field) {
            return this.preview.options[field.uid] || [];
        };
        PreviewCtrl.prototype.getScreen = function () {
            return this.preview.screen;
        };
        PreviewCtrl.prototype.getSubmissionSubtitle = function (submission) {
            return this.settings.contest.submissions.subtitle;
        };
        PreviewCtrl.prototype.getSubmissionTitle = function (submission) {
            return this.$scope.$eval(this.settings.contest.submissions.title, submission);
        };
        PreviewCtrl.prototype.getSubmissionWidth = function () {
            return '33.33333333333333333%';
        };
        PreviewCtrl.prototype.getSubmissions = function () {
            return this.preview.submissions;
        };
        PreviewCtrl.prototype.isLayout = function (layout) {
            return this.preview.layout === layout;
        };
        PreviewCtrl.prototype.isScreen = function (screen) {
            return this.preview.screen === screen;
        };
        PreviewCtrl.prototype.parseOptionsOf = function (field) {
            this.preview.options[field.uid] = field.options.split('\n').map(function (item) {
                return { label: item.replace(/(.*?\s*:+\s*)/g, '') };
            });
        };
        PreviewCtrl.prototype.randomNumber = function (min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        };
        PreviewCtrl.prototype.setLayout = function (layout) {
            this.preview.layout = layout;
        };
        PreviewCtrl.prototype.setScreen = function (screen) {
            this.preview.screen = screen;
        };
        PreviewCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], PreviewCtrl);
        return PreviewCtrl;
    }());
    TotalContest.PreviewCtrl = PreviewCtrl;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var RepeaterCtrl = /** @class */ (function () {
        function RepeaterCtrl() {
            this.items = [];
        }
        RepeaterCtrl.prototype.addItem = function (item) {
            if (item === void 0) { item = {}; }
            this.items.push(item);
        };
        RepeaterCtrl.prototype.deleteItem = function (index) {
            this.items.splice(index, 1);
        };
        RepeaterCtrl.prototype.moveDown = function (index) {
            this.moveUp(index + 1);
        };
        RepeaterCtrl.prototype.moveUp = function (index) {
            if (index === 0 || index === this.items.length) {
                return;
            }
            this.items.splice(index - 1, 0, this.items[index]);
            this.items.splice(index + 1, 1);
        };
        RepeaterCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], RepeaterCtrl);
        return RepeaterCtrl;
    }());
    TotalContest.RepeaterCtrl = RepeaterCtrl;
})(TotalContest || (TotalContest = {}));
///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Providers;
        (function (Providers) {
            var I18nService = /** @class */ (function () {
                function I18nService(SettingsService) {
                    this.SettingsService = SettingsService;
                }
                I18nService.prototype.__ = function (expression) {
                    return this.SettingsService.i18n[expression] || expression;
                };
                I18nService = __decorate([
                    Common.Service('services.common')
                ], I18nService);
                return I18nService;
            }());
            Providers.I18nService = I18nService;
        })(Providers = Common.Providers || (Common.Providers = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/filters/slug.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/i18n.ts" />
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var Slug = TotalCore.Common.Filters.Slug;
    var ContestPagesCtrl = /** @class */ (function () {
        function ContestPagesCtrl($scope, SettingsService, UniqueIdService, I18nService) {
            this.$scope = $scope;
            this.SettingsService = SettingsService;
            this.UniqueIdService = UniqueIdService;
            this.I18nService = I18nService;
            $scope.pages = this.SettingsService.settings['pages'].other;
            $scope.collapsed = {};
            $scope.$watch('pages', function (pages, oldPages) {
                angular.forEach(pages, function (page, pageIndex) {
                    page.id = Slug.filter(page.title, '-');
                });
            }, true);
        }
        ContestPagesCtrl.prototype.collapsePages = function () {
            this.setCollapsedForAll(true);
        };
        ContestPagesCtrl.prototype.deleteFields = function () {
            if (confirm(this.I18nService.__(['Are you sure?']))) {
                this.SettingsService.settings['pages'].other = [];
            }
        };
        ContestPagesCtrl.prototype.deletePage = function (index, ask, $event) {
            if (ask === void 0) { ask = false; }
            if (ask && !confirm(this.I18nService.__('Are you sure?'))) {
                $event.stopPropagation();
                return false;
            }
            this.SettingsService.settings['pages'].other.splice(index, 1);
            return false;
        };
        ContestPagesCtrl.prototype.expandPages = function () {
            this.setCollapsedForAll(false);
        };
        ContestPagesCtrl.prototype.insertPage = function (args) {
            this.collapsePages();
            args = angular.extend({}, {
                id: 'untitled',
                title: 'Untitled',
                content: '',
                collapsed: false,
            }, args);
            this.SettingsService.settings['pages'].other.push(args);
        };
        ContestPagesCtrl.prototype.isCollapsed = function (index) {
            return this.$scope.collapsed.hasOwnProperty(index) ? this.$scope.collapsed[index] : true;
        };
        ContestPagesCtrl.prototype.setCollapsedForAll = function (collapsed) {
            this.SettingsService.settings['pages'].other.forEach(function (item) {
                item.collapsed = collapsed;
            });
        };
        ContestPagesCtrl.prototype.toggle = function (index, $event) {
            if ($event) {
                $event.preventDefault();
                $event.stopPropagation();
            }
            this.$scope.collapsed[index] = this.$scope.collapsed.hasOwnProperty(index) ? !this.$scope.collapsed[index] : false;
            // this.SettingsService.settings['pages'].other[index].collapsed = !this.SettingsService.settings['pages'].other[index].collapsed;
            return false;
        };
        ContestPagesCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], ContestPagesCtrl);
        return ContestPagesCtrl;
    }());
    TotalContest.ContestPagesCtrl = ContestPagesCtrl;
})(TotalContest || (TotalContest = {}));
///<reference path="../../common/decorators.ts"/>
var TotalCore;
(function (TotalCore) {
    var Common;
    (function (Common) {
        var Providers;
        (function (Providers) {
            var UniqueIdService = /** @class */ (function () {
                function UniqueIdService() {
                }
                /**
                 * Generate GUID
                 * @see http://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript
                 * @returns {string}
                 */
                UniqueIdService.prototype.generate = function () {
                    var d = new Date().getTime();
                    if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                        d += performance.now(); //use high-precision timer if available
                    }
                    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                        var r = (d + Math.random() * 16) % 16 | 0;
                        d = Math.floor(d / 16);
                        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                    });
                };
                UniqueIdService = __decorate([
                    Common.Service('services.common')
                ], UniqueIdService);
                return UniqueIdService;
            }());
            Providers.UniqueIdService = UniqueIdService;
        })(Providers = Common.Providers || (Common.Providers = {}));
    })(Common = TotalCore.Common || (TotalCore.Common = {}));
})(TotalCore || (TotalCore = {}));
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/filters/slug.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/uid.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/i18n.ts" />
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var Slug = TotalCore.Common.Filters.Slug;
    var FormFieldsCtrl = /** @class */ (function () {
        function FormFieldsCtrl($scope, ajaxEndpoint, prefix, SettingsService, UniqueIdService, I18nService) {
            this.$scope = $scope;
            this.ajaxEndpoint = ajaxEndpoint;
            this.prefix = prefix;
            this.SettingsService = SettingsService;
            this.UniqueIdService = UniqueIdService;
            this.I18nService = I18nService;
            this.categories = [];
            this.fields = this.SettingsService.settings['contest'].form.fields;
            $scope.fields = this.SettingsService.settings['contest'].form.fields;
            $scope.$watch('fields', function (fields) {
                var fieldsNames = [];
                angular.forEach(fields, function (field) {
                    if (field.name.trim() === '') {
                        field.name = field.label || "untitled_" + UniqueIdService.generate().substr(0, 4);
                    }
                    field.name = Slug.filter(field.name, '_', true);
                    if (fieldsNames.indexOf(field.name) != -1) {
                        field.name = field.name + "_copy}";
                    }
                    fieldsNames.push(field.name);
                });
            }, true);
        }
        FormFieldsCtrl.prototype.collapseFields = function () {
            this.setCollapsedForAll(true);
        };
        FormFieldsCtrl.prototype.deleteField = function (index, ask, $event) {
            if (ask === void 0) { ask = false; }
            if (ask && !confirm(this.I18nService.__(['Are you sure?']))) {
                $event.stopPropagation();
                return false;
            }
            this.$scope.fields.splice(index, 1);
            return false;
        };
        FormFieldsCtrl.prototype.deleteFields = function () {
            if (confirm(this.I18nService.__(['Are you sure?']))) {
                var fieldsCount = this.$scope.fields.length;
                for (var index = 0; index < fieldsCount; index++) {
                    this.$scope.fields.pop();
                }
            }
        };
        FormFieldsCtrl.prototype.expandFields = function () {
            this.setCollapsedForAll(false);
        };
        FormFieldsCtrl.prototype.generateName = function (field) {
            if (!field.name) {
                field.name = Slug.filter(field.label, '_');
            }
        };
        FormFieldsCtrl.prototype.hasCategoryField = function () {
            for (var _i = 0, _a = this.$scope.fields; _i < _a.length; _i++) {
                var field = _a[_i];
                if (field.type === 'category') {
                    return true;
                }
            }
            return false;
        };
        FormFieldsCtrl.prototype.insertField = function (args) {
            this.collapseFields();
            args = angular.extend({}, {
                uid: this.UniqueIdService.generate(),
                type: 'text',
                name: '',
                label: 'Field',
                defaultValue: '',
                options: '',
                collapsed: false,
                validations: {},
                attributes: {},
                template: '',
            }, args);
            this.$scope.fields.push(args);
        };
        FormFieldsCtrl.prototype.isCollapsed = function (index) {
            return Boolean(this.$scope.fields[index].collapsed);
        };
        FormFieldsCtrl.prototype.normalizeField = function (field) {
            if (angular.isArray(field.validations)) {
                field.validations = {};
            }
            if (angular.isArray(field.attributes)) {
                field.attributes = {};
            }
        };
        FormFieldsCtrl.prototype.refreshCategories = function () {
            var _this = this;
            jQuery
                .get(this.ajaxEndpoint, { action: this.prefix + "_contests_get_categories" })
                .then(function (response) {
                _this.$scope.$applyAsync(function () {
                    _this.categories = response.data;
                });
            });
        };
        FormFieldsCtrl.prototype.setCollapsedForAll = function (collapsed) {
            this.$scope.fields.forEach(function (item) {
                item.collapsed = collapsed;
            });
        };
        FormFieldsCtrl.prototype.toggle = function (index, $event) {
            if ($event) {
                $event.preventDefault();
                $event.stopPropagation();
            }
            this.$scope.fields[index].collapsed = !this.$scope.fields[index].collapsed;
            return false;
        };
        FormFieldsCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], FormFieldsCtrl);
        return FormFieldsCtrl;
    }());
    TotalContest.FormFieldsCtrl = FormFieldsCtrl;
})(TotalContest || (TotalContest = {}));
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var VoteCriterion = /** @class */ (function () {
        function VoteCriterion(name) {
            this.name = name;
        }
        return VoteCriterion;
    }());
    var VoteCriteriaCtrl = /** @class */ (function () {
        //@PRO
        function VoteCriteriaCtrl($scope, $window, SettingsService) {
            this.$scope = $scope;
            this.$window = $window;
            this.SettingsService = SettingsService;
            $scope.criteria = this.SettingsService.settings['vote'].criteria;
        }
        VoteCriteriaCtrl.prototype.add = function (type) {
            var criterion = new VoteCriterion('Untitled');
            this.SettingsService.settings['vote'].criteria.push(criterion);
        };
        VoteCriteriaCtrl.prototype.remove = function (index, ask, $event) {
            if (ask === void 0) { ask = false; }
            if (ask && !this.$window.confirm('Are you sure?')) {
                $event.stopPropagation();
                return false;
            }
            this.SettingsService.settings['vote'].criteria.splice(index, 1);
            return false;
        };
        VoteCriteriaCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], VoteCriteriaCtrl);
        return VoteCriteriaCtrl;
    }());
    TotalContest.VoteCriteriaCtrl = VoteCriteriaCtrl;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var SidebarIntegrationCtrl = /** @class */ (function () {
        function SidebarIntegrationCtrl(ajaxEndpoint, prefix, SettingsService) {
            this.ajaxEndpoint = ajaxEndpoint;
            this.prefix = prefix;
            this.SettingsService = SettingsService;
            this.sidebars = this.SettingsService.information.sidebars || {};
        }
        SidebarIntegrationCtrl.prototype.addWidgetToSidebar = function () {
            var _this = this;
            this.sidebar.inserted = true;
            jQuery.post(this.ajaxEndpoint, {
                action: this.prefix + "_contests_add_to_sidebar",
                contest: this.SettingsService.settings.id,
                sidebar: this.sidebar.id
            }).then(function (response) { return _this.sidebar.inserted = response.success || false; })
                .fail(function () { return _this.sidebar.inserted = false; });
        };
        SidebarIntegrationCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], SidebarIntegrationCtrl);
        return SidebarIntegrationCtrl;
    }());
    TotalContest.SidebarIntegrationCtrl = SidebarIntegrationCtrl;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var Controller = TotalCore.Common.Controller;
    var NotificationsCtrl = /** @class */ (function () {
        //@PRO
        function NotificationsCtrl($rootScope, SettingsService) {
            this.$rootScope = $rootScope;
            this.SettingsService = SettingsService;
            this.pushCompleted = false;
        }
        NotificationsCtrl.prototype.setupPushService = function () {
            var _this = this;
            jQuery.ajax({
                url: "https://cdn.onesignal.com/sdks/OneSignalSDK.js",
                dataType: "script",
                cache: true,
                success: function () {
                    if (!window['OneSignal'].isPushNotificationsSupported()) {
                        _this.pushCompleted = true;
                        return alert(window['TotalContest18n']['Unfortunately, your browser does not support push notifications.']);
                    }
                    window['OneSignal'].init({
                        appId: _this.SettingsService.settings['notifications']['push']['appId'],
                        autoRegister: true,
                        promptOptions: {
                            actionMessage: window['TotalContest18n']['Do you want to receive notifications from TotalContest?'],
                            acceptButtonText: window['TotalContest18n']['Yes'],
                            cancelButtonText: window['TotalContest18n']['No']
                        }
                    });
                    window['OneSignal'].isPushNotificationsEnabled(function (isEnabled) {
                        if (isEnabled) {
                            _this.pushCompleted = true;
                            _this.$rootScope.$applyAsync();
                        }
                        else {
                            window['OneSignal'].showHttpPrompt();
                        }
                    });
                    window['OneSignal'].on('subscriptionChange', function (isSubscribed) {
                        _this.pushCompleted = isSubscribed;
                    });
                }
            });
        };
        ;
        NotificationsCtrl = __decorate([
            Controller('controllers.totalcontest')
        ], NotificationsCtrl);
        return NotificationsCtrl;
    }());
    TotalContest.NotificationsCtrl = NotificationsCtrl;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var Component = TotalCore.Common.Component;
    var ProgressiveTextareaComponent = /** @class */ (function () {
        function ProgressiveTextareaComponent() {
            this.mode = 'simple';
        }
        ProgressiveTextareaComponent.prototype.isAdvanced = function () {
            return this.mode === 'advanced';
        };
        ProgressiveTextareaComponent.prototype.isSimple = function () {
            return this.mode === 'simple';
        };
        ProgressiveTextareaComponent.prototype.switchToAdvanced = function () {
            this.mode = 'advanced';
        };
        ProgressiveTextareaComponent.prototype.switchToSimple = function () {
            this.mode = 'simple';
        };
        ProgressiveTextareaComponent = __decorate([
            Component('components.totalcontest', {
                templateUrl: 'progressive-textarea-template',
                bindings: {
                    model: '=ngModel',
                    rows: '@',
                }
            })
        ], ProgressiveTextareaComponent);
        return ProgressiveTextareaComponent;
    }());
})(TotalContest || (TotalContest = {}));
///<reference path="../../../../build/typings/index.d.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/decorators.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/configs/Global.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/configs/Http.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/providers/settings.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/directives/tab.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/directives/datetimepicker.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/directives/colorpicker.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/directives/tinymce.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/directives/copy-to-clipboard.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/filters/slug.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/common/filters/i18n.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/customizer/components/customizer.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/customizer/components/customizer-control.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/customizer/components/customizer-tabs.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/customizer/components/customizer-tab.ts" />
///<reference path="../../../../vendor/misqtech/totalsuite-totalcore/assets/scripts/customizer/components/customizer-tab-content.ts" />
///<reference path="controllers/editor.ts" />
///<reference path="controllers/preview.ts" />
///<reference path="controllers/repeater.ts" />
///<reference path="controllers/contest-pages.ts" />
///<reference path="controllers/form-fields.ts" />
///<reference path="controllers/vote-criteria.ts" />
///<reference path="controllers/sidebar-integration.ts" />
///<reference path="controllers/notifications.ts" />
///<reference path="components/progressive-textarea.ts" />
var TotalContest;
(function (TotalContest) {
    var HttpConfig = TotalCore.Common.Configs.HttpConfig;
    var GlobalConfig = TotalCore.Common.Configs.GlobalConfig;
    TotalContest.editor = angular
        .module('contest-editor', [
        'dndLists',
        'ngResource',
        'services.common',
        'directives.common',
        'components.customizer',
        'filters.common',
        'controllers.totalcontest',
        'components.totalcontest',
    ])
        .config(GlobalConfig)
        .config(HttpConfig)
        .value('ajaxEndpoint', window['ajaxurl'] || '/wp-admin/admin-ajax.php')
        .value('namespace', 'TotalContest')
        .value('prefix', 'totalcontest');
})(TotalContest || (TotalContest = {}));

//# sourceMappingURL=maps/contest-editor.js.map
