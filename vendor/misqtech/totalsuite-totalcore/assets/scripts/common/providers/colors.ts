///<reference path="../../common/decorators.ts"/>
///<reference path="../../common/helpers.ts"/>
namespace TotalCore.Common.Providers {
    @Service('services.common')
    export class ColorsService {
        private colors = ["#f44336", "#e91e63", "#9c27b0", "#673ab7", "#3f51b5", "#2196f3", "#03a9f4", "#00bcd4", "#009688", "#4caf50", "#8bc34a", "#cddc39", "#ffeb3b", "#ffc107", "#ff9800", "#ff5722", "#795548", "#9e9e9e", "#607d8b", "#ffb900", "#e74856", "#0078d7", "#0099bc", "#7a7574", "#767676", "#ff8c00", "#e81123", "#0063b1", "#2d7d9a", "#5d5a58", "#4c4a48", "#f7630c", "#ea005e", "#8e8cd8", "#00b7c3", "#68768a", "#69797e", "#ca5010", "#c30052", "#6b69d6", "#038387", "#515c6b", "#4a5459", "#da3b01", "#e3008c", "#8764b8", "#00b294", "#567c73", "#647c64", "#ef6950", "#bf0077", "#744da9", "#018574", "#486860", "#525e54", "#d13438", "#c239b3", "#b146c2", "#00cc6a", "#498205", "#847545", "#ff4343", "#9a0089", "#881798", "#10893e", "#107c10", "#7e735f", "#a4c400", "#60a917", "#008a00", "#00aba9", "#1ba1e2", "#0050ef", "#6a00ff", "#aa00ff", "#f472d0", "#d80073", "#a20025", "#e51400", "#fa6800", "#f0a30a", "#e3c800", "#825a2c", "#6d8764", "#647687", "#76608a", "#a0522d"];

        constructor() {

        }

        getRandomColors(count: number) {
            this.colors = shuffle(this.colors);
            return this.colors.slice(0, count > this.colors.length ? this.colors.length : count);
        }
    }
}