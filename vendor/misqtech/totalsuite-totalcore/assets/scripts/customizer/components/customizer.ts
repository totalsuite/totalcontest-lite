namespace TotalCore.Customizer.Components {
    import Component = TotalCore.Common.Component;
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Component('components.customizer', {
        templateUrl: 'customizer-component-template',
        bindings: {}
    })
    class CustomizerComponent {
        public device = 'laptop';
        public devices = {
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
        public iframe;
        public preview = {
            screen: '',
        };
        public settings: any = {};
        public tab = [];

        constructor(private $scope, private $compile, private $templateCache, private $http, private $sce, private $element, private $q, private SettingsService: SettingsService) {
            this.settings = SettingsService.settings.design || this.settings;
            this.iframe = $element.find('iframe').contents();

            if (!this.SettingsService.templates[this.getTemplate()]) {
                this.setTemplate(this.SettingsService.defaults.design.template);
            }

            window['jQuery'](window).resize(() => {
                $scope.$applyAsync();
            });
        }

        $onInit() {
            this.preparePreview();
        }

        changeTemplateTo(template, $event) {
            $event.stopPropagation();
            this.setTemplate(template.id);
            this.popToRoot();
            this.preparePreview();
        }

        escape(content) {
            return this.$sce.trustAsHtml(content);
        }

        getActiveTab() {
            return this.tab[this.tab.length - 1];
        }

        getActiveTabBreadcrumb() {
            return this.tab
                .map(function (tab) {
                    return tab.label;
                })
                .join(' / ');
        }

        getCurrentTemplate(field?) {
            let template = this.SettingsService.templates[this.getTemplate()];
            return field ? template[field] : template;
        }

        getCurrentTemplateDefaults() {
            if (this.getCurrentTemplate('defaults')) {
                return this.$http.get(this.prefixUrl(this.getCurrentTemplate('defaults')), {cache: true}).then(response => response.data);
            }
            return this.$q.resolve({});
        }

        getCurrentTemplatePreviewContentId() {
            return this.getCurrentTemplate('preview') ? this.prefixUrl(this.getCurrentTemplate('preview')) : null;
        }

        getCurrentTemplatePreviewCssId() {
            return this.getCurrentTemplate('stylesheet') ? this.prefixUrl(this.getCurrentTemplate('stylesheet')) : null;
        }

        getCurrentTemplateSettingsId() {
            return this.getCurrentTemplate('settings') ? this.prefixUrl(this.getCurrentTemplate('settings')) : null;
        }

        getDevice() {
            return this.device;
        }

        getDeviceScaleAttributes() {
            if (this.device === 'laptop') {
                return {};
            }

            let scale = this.$element.find('iframe').parent().outerHeight() / this.devices[this.device].height;
            return {
                transform: `scale(${scale})`,
                marginTop: this.devices[this.device].canvas.top * scale,
            };
        }

        getScreen() {
            return this.preview.screen;
        }

        getTemplate() {
            return this.settings.template;
        }

        getTemplates() {
            return this.SettingsService.templates;
        }

        hasActiveTab(tab) {
            if (tab) {
                for (let item of this.tab) {
                    if (item.id === tab) {
                        return true;
                    }
                }
                return false;
            } else {
                return this.tab.length > 0;
            }
        }

        hasActiveTabAfter(tab) {
            return this.hasActiveTab(tab) && this.getActiveTab().id !== tab;
        }

        isActiveTab(tab) {
            if (this.tab.length > 0) {
                return this.tab[this.tab.length - 1].id === tab;
            }
            return false;
        }

        isDevice(device) {
            return this.device === device;
        }

        isScreen(screen) {
            return this.preview.screen === screen;
        }

        isTemplate(template) {
            return this.settings.template === template;
        }

        popActiveTab() {
            this.tab.pop();
        }

        popToRoot() {
            this.tab = [];
        }

        preparePreview() {
            var headTemplate = this.$templateCache.get('customizer-preview-head-template');
            var bodyTemplate = this.$templateCache.get('customizer-preview-body-template');
            var compiledHeadTemplate = this.$compile(headTemplate);
            var compiledBodyTemplate = this.$compile(bodyTemplate);

            this.getCurrentTemplateDefaults()
                .then(defaults => {
                    this.settings.custom = angular.extend({}, defaults, this.settings.custom);
                    this.$scope.custom = this.settings.custom;

                    angular.forEach(this.settings, (value, key) => {
                        this.$scope[key] = value;
                    });
                });

            this.iframe.html('');
            this.iframe.find('head').append(compiledHeadTemplate(this.$scope));
            this.iframe.find('body').html(compiledBodyTemplate(this.$scope));
        }

        resetActiveTab() {
            this.tab = [];
        }

        resetToDefaults(confirmBefore = false) {
            if (confirmBefore && !confirm('Are you sure?')) {
                return;
            }

            this.getCurrentTemplateDefaults()
                .then(defaults => {
                    this.settings.custom = angular.extend({}, this.settings.custom, defaults);
                    this.$scope.custom = this.settings.custom;
                });

            if (confirmBefore) {
                alert('Done');
            }
        }

        setActiveTab(tab, label) {
            this.tab.push({id: tab, label: label});
        }

        setDevice(device) {
            this.device = device;
        }

        setScreen(screen) {
            this.preview.screen = screen;
        }

        setTemplate(template) {
            this.settings.template = template;
            this.resetToDefaults();
        }

        private prefixUrl(url) {
            if (url.match(/^(https?:)?\/\//g)) {
                return url;
            }

            return `${this.getCurrentTemplate('url')}${url}`;
        }
    }
}