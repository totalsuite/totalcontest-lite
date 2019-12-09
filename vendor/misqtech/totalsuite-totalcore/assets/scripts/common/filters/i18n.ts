///<reference path="../../common/decorators.ts"/>
///<reference path="../providers/settings.ts"/>
namespace TotalCore.Common.Filters {
    import SettingsService = TotalCore.Common.Providers.SettingsService;

    @Filter('filters.common', 'i18n')
    export class I18n {

        constructor(SettingsService: SettingsService) {
            return (input: any, separator: string = '-') => {
                return I18n.filter(input, separator, SettingsService.i18n);
            }
        }

        public static filter(name, separator, expressions) {
            if (name && name.toString) {
                return expressions[name] || name;
            }
        }

    }
}