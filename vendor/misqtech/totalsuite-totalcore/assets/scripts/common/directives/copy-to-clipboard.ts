///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {
    @Directive('directives.common', 'copyToClipboard')
    export class CopyToClipboard {
        constructor() {
            var copyToClipboard = function (text) {
                if (window['clipboardData'] && window['clipboardData'].setData) {
                    return window['clipboardData'].setData("Text", text);
                } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                    var textarea = document.createElement("textarea");
                    textarea.textContent = text;
                    textarea.style.position = "fixed";
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        return document.execCommand("copy");
                    } catch (ex) {
                        prompt('', text);
                        return false;
                    } finally {
                        document.body.removeChild(textarea);
                    }
                }
            };
            return {
                restrict: 'A',
                link: ($scope, element: any, attributes: any) => {
                    element.on('click', () => {
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
    }
}