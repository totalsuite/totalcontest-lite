///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Providers {
    @Service('services.common')
    export class SettingsService {
        public account: any = window[`${this.namespace}Account`] || [];
        public activation: any = window[`${this.namespace}Activation`] || [];
        public defaults: any = window[`${this.namespace}Defaults`] || {};
        public i18n: any = window[`${this.namespace}I18n`] || [];
        public information: any = window[`${this.namespace}Information`] || {};
        public languages: any = window[`${this.namespace}Languages`] || [];
        public modules: any = window[`${this.namespace}Modules`] || {};
        public presets: any = window[`${this.namespace}Presets`] || [];
        public settings: any = window[`${this.namespace}Settings`] || {};
        public support: any = window[`${this.namespace}Support`] || [];
        public templates: any = window[`${this.namespace}Templates`] || {};
        public versions: any = window[`${this.namespace}Versions`] || [];

        constructor(public namespace, public prefix) {
            this.settings['id'] = this.defaults['id'];
            this.settings = angular.merge({}, this.defaults, this.settings);
        }
    }
}