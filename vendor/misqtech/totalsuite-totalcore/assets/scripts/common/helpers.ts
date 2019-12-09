/**
 * Helpers.
 */
namespace TotalCore.Common {
    /**
     * Extraction type.
     */
    export enum EXTRACT_TYPE {
        Values,
        Keys
    }

    /**
     * Extract values/keys of object.
     *
     * @param object
     * @param {TotalCore.EXTRACT_TYPE} extract
     * @returns {any[]}
     * @private
     */
    export function extract(object, extract: EXTRACT_TYPE) {
        let values = [];
        angular.forEach(object, (value, key) => values.push(extract === EXTRACT_TYPE.Values ? value : key));
        return values;
    }

    /**
     * Shuffle array.
     *
     * @param {Array} array
     * @returns {Array}
     */
    export function shuffle(array: Array<any>) {
        var currentIndex = array.length, temporaryValue, randomIndex;
        while (0 !== currentIndex) {
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;

            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
        }

        return array;
    }

    /**
     * Processable trait.
     */
    export abstract class Processable {
        protected processed: boolean = false;
        protected processing: boolean = false;

        /**
         * Check processed.
         * @returns {boolean}
         */
        isProcessed() {
            return this.processed;
        }

        /**
         * Check processing.
         * @returns {boolean}
         */
        isProcessing() {
            return this.processing;
        }

        setProcessed(processed: boolean) {
            this.processed = processed;
        }

        /**
         * Start processing.
         */
        startProcessing() {
            this.processing = true;
        }

        /**
         * Stop processing.
         */
        stopProcessing() {
            this.processing = false;
        }
    }

    /**
     * Progressive trait.
     */
    export abstract class Progressive extends Processable {
        protected progress: Number | Boolean = false;

        /**
         * Get progress.
         * @returns {Number | Boolean}
         */
        getProgress() {
            return this.progress;
        }

        /**
         * Set progress.
         * @param {Number | Boolean} progress
         */
        setProgress(progress: Number | Boolean) {
            this.progress = progress;
        }
    }

    /**
     * Paginated table.
     */
    export abstract class PaginatedTable {
        protected pagination = {
            page: 1,
            total: 1,
        };

        abstract fetchPage(page: number): Promise<any>;

        getPage() {
            return this.pagination.page;
        }

        getTotalPages() {
            return this.pagination.total;
        }

        hasNextPage() {
            return !this.isLastPage();
        }

        hasPreviousPage() {
            return !this.isFirstPage();
        }

        isFirstPage() {
            return this.isPage(1);
        }

        isLastPage() {
            return this.getPage() == this.getTotalPages();
        }

        isPage(page: number) {
            return this.getPage() == page;
        }

        nextPage() {
            let nextPage = this.getPage() + 1;
            return this.fetchPage(nextPage)
                .then((result) => {
                    this.setPage(nextPage);
                    return result;
                });
        }

        previousPage() {
            let previousPage = this.pagination.page + 1;
            return this.fetchPage(previousPage)
                .then((result) => {
                    this.setPage(previousPage);
                    return result;
                });
        }

        setPage(page: number) {
            this.pagination.page = Math.abs(page);
        }

        setTotalPages(total: number) {
            this.pagination.total = Math.abs(total) || 1;
        }
    }


    /**
     * Transitions
     */
    export abstract class Transition {
        public duration: number = 500;
        public element;

        constructor(element, duration: number = 500) {
            this.element = window['jQuery'](element);
        }

        getDuration() {
            return this.duration;
        }

        getElement() {
            return this.element;
        }

        abstract in(callback?: Function, duration?: number);

        abstract out(callback?: Function, duration?: number);
    }

    export class SimpleTransition extends Transition {
        in(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().css({'visibility': 'visible', 'display': 'inherit'});
            if (callback) {
                callback();
            }
        }

        out(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().css('visibility', 'hidden');
            if (callback) {
                callback();
            }
        }
    }

    export class FadeTransition extends Transition {
        in(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().fadeIn(duration, callback);
        }

        out(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().fadeTo(duration, 0.00001, callback);
        }
    }

    export class SlideTransition extends Transition {
        in(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().slideDown(duration, callback);
        }

        out(callback?: Function, duration: number = this.getDuration()) {
            this.getElement().slideUp(duration, callback);
        }
    }
}