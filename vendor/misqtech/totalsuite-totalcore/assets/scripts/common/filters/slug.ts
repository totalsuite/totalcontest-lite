///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Filters {
    @Filter('filters.common', 'slug')
    export class Slug {
        constructor() {
            return (input: any, separator: string = '-') => {
                return Slug.filter(input, separator);
            }
        }

        public static $inject = [];
        public static specialChars = ["!", "@", "#", "$", "%", "^", "&", "*", "<", ">", ":", ".", ";", ",", "!", "?", "§", "¨", "£", "-", "_", "(", ")", "{", "}", "[", "]", "=", "+", "|", "~", "`", "'", "°", '"', "/", "\\"];

        public static filter(name, separator, keepEdge = false) {
            if (name && name.toString) {
                return name
                    .toString()
                    .toLowerCase()
                    .replace(/\s+/g, separator)           // Replace spaces with -
                    .replace(new RegExp(`[\\${Slug.specialChars.join('\\')}]+`.replace(`\\${separator}`, ''), 'g'), '')       // Remove all non-word chars
                    .replace(new RegExp(`\\${separator}\\${separator}+`, 'g'), separator)         // Replace multiple - with single -
                    .replace(keepEdge ? null : new RegExp(`^\\${separator}+`), '')             // Trim - from start of text
                    .replace(keepEdge ? null : new RegExp(`\\${separator}+$`), '');            // Trim - from end of text
            }
        }

    }
}