///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/providers/settings.ts"/>
namespace TotalCore.Common.Providers {
    @Service('services.common')
    export class I18nService {
        constructor(private SettingsService: SettingsService) {

        }

        __(expression) {
            return this.SettingsService.i18n[expression] || expression;
        }
    }
}