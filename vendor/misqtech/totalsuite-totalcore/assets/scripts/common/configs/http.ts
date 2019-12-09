///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Configs {
    import IHttpProvider = angular.IHttpProvider;
    import ICompileProvider = angular.ICompileProvider;

    @Injectable()
    export class HttpConfig {
        constructor($resourceProvider, $httpProvider: IHttpProvider, $compileProvider: ICompileProvider) {
            // Don't strip trailing slashes from calculated URLs
            $resourceProvider.defaults.stripTrailingSlashes = false;

            $httpProvider.defaults.transformRequest = (data) => {
                if (data === undefined) {
                    return data;
                }

                return HttpConfig.serializer(new FormData(), data);
            };

            $httpProvider.defaults.headers.post['Content-Type'] = undefined;

            $compileProvider.debugInfoEnabled(false);
        }


        public static serializer(form, fields, parent?) {
            angular.forEach(fields, function (fieldValue, fieldName) {
                if (parent) {
                    fieldName = `${parent}[${fieldName}]`;
                }
                if (fieldValue !== null && typeof fieldValue === 'object' && (fieldValue.__proto__ === Object.prototype || fieldValue.__proto__ === Array.prototype)) {
                    HttpConfig.serializer(form, fieldValue, fieldName);
                } else {
                    if (typeof fieldValue === 'boolean') {
                        fieldValue = Number(fieldValue);
                    } else if (fieldValue === null) {
                        fieldValue = '';
                    }

                    form.append(fieldName, fieldValue);
                }
            });

            return form;
        }

    }
}