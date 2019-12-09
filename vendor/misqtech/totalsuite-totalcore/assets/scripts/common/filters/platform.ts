///<reference path="../../common/decorators.ts"/>
///<reference path="../helpers.ts"/>
namespace TotalCore.Common.Filters {

    @Filter('filters.common', 'platform')
    export class Platform {
        constructor() {
            return (input: any) => {
                return Platform.filter(input);
            }
        }

        public static filter(value) {
            return window['platform'].parse(value);
        }
    }
}