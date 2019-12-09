///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Providers {
    @Service('services.common')
    export class UniqueIdService {
        constructor() {

        }

        /**
         * Generate GUID
         * @see http://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript
         * @returns {string}
         */
        generate() {
            var d = new Date().getTime();
            if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                d += performance.now(); //use high-precision timer if available
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }
    }
}