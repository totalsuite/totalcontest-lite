///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Providers {
    import IQService = angular.IQService;
    import IHttpService = angular.IHttpService;
    let wp = window['wp'];

    @Service('services.common')
    export class EmbedService {
        public providersUrlPatterns = [
            /https?:\/\/((m|www)\.)?youtube\.com\/watch.*/i,
            /https?:\/\/((m|www)\.)?youtube\.com\/playlist.*/i,
            /https?:\/\/youtu\.be\/.*/i,
            /https?:\/\/(.+\.)?vimeo\.com\/.*/i,
            /https?:\/\/(www\.)?dailymotion\.com\/.*/i,
            /https?:\/\/dai\.ly\/.*/i,
            /https?:\/\/(www\.)?(animoto|video214)\.com\/play\/.*/i,
            /https?:\/\/(www\.)?twitter\.com\/i\/moments\/.*/i,
            /https?:\/\/www\.facebook\.com\/video\.php.*/i,
            /https?:\/\/www\.facebook\.com\/.*\/videos\/.*/i,
            /https?:\/\/vine\.co\/v\/.*/i,
            /https?:\/\/(www\.)?mixcloud\.com\/.*/i,
            /https?:\/\/(www\.)?reverbnation\.com\/.*/i,
            /https?:\/\/(www\.)?soundcloud\.com\/.*/i,
            /https?:\/\/(open|play)\.spotify\.com\/.*/i,
        ];

        constructor(private $q: IQService, private $http: IHttpService) {

        }

        discover(url) {
            return this.$http
                .get(
                    'https://noembed.com/embed',
                    {
                        params: {
                            format: 'json',
                            url: url,
                        },
                        cache: true,
                        responseType: 'json'
                    }
                )
                .then((response) => {
                    return response.data;
                });
        }

        fetch(url) {
            return this.$q((resolve, reject) => {
                for (let regExp of this.providersUrlPatterns) {
                    if (regExp.exec(url)) {
                        return this.discover(url).then((response) => {
                            resolve(response);
                        }).catch(reject);
                    }
                }

                reject()
            });
        }

    }
}