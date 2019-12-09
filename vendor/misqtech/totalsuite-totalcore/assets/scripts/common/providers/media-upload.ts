///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Providers {
    import IPromise = angular.IPromise;
    import IQService = angular.IQService;
    let wp = window['wp'];

    @Service('services.common')
    export class MediaUploadService {
        public wpMediaFrame;

        constructor(private $q: IQService) {

        }

        customFrame(args) {
            return wp.media(angular.extend({}, {title: wp.media.view.l10n.insertMediaTitle}, args));
        }

        frame(type) {
            if (!this.wpMediaFrame) {
                this.wpMediaFrame = wp.media({
                    title: wp.media.view.l10n.insertMediaTitle,
                    multiple: false,
                    library: {
                        type: type
                    }
                });
            }

            if (type !== this.wpMediaFrame.options.library.type) {
                this.wpMediaFrame = false;
                this.frame(type);
            }

            return this.wpMediaFrame;
        }

        open(type): IPromise<any> {
            return this.$q((resolve, reject) => {
                try {
                    this.frame(type).open();
                    this.wpMediaFrame
                        .state('library')
                        .off('select')
                        .on('select', function () {
                            resolve(this.get('selection').first());
                        });
                } catch (exception) {
                    reject(exception);
                }
            });
        }

    }
}