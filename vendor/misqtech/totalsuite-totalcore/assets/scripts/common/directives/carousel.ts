///<reference path="../../common/decorators.ts"/>
namespace TotalCore.Common.Directives {

    @Directive('directives.common', 'carousel')
    export class Carousel {
        constructor() {
            return {
                restrict: 'A',
                link: ($scope, element: any, attributes: any) => {
                    let $slides = element.find('[carousel-slides-item]');
                    let $controls = element.find('[carousel-controls-item]');
                    let autoSlidingInterval;

                    let startAutoSliding = function () {
                        if (!autoSlidingInterval) {
                            moveToNext();
                        } else {
                            clearInterval(autoSlidingInterval);
                        }
                        autoSlidingInterval = setInterval(() => moveToNext(), 5000);
                    };

                    let stopAutoSliding = function () {
                        clearInterval(autoSlidingInterval);
                    };

                    let moveToNext = function () {
                        moveTo($slides.filter('.active').index() + 1);
                    };

                    let moveTo = function (offset) {
                        let $current = $slides.filter('.active');

                        if ($current.index() === offset) {
                            return;
                        }

                        if (offset >= $slides.length) {
                            offset = 0;
                        }

                        $slides.removeClass('previous');
                        $slides.removeClass('active');

                        $slides.eq(offset).addClass('active');
                        $current.addClass('previous');
                        setTimeout(() => {
                            $current.removeClass('previous')
                        }, 750);

                        $controls.removeClass('active');
                        $controls.eq(offset).addClass('active');
                    };

                    $controls.on('click', function (event) {
                        moveTo(angular.element(event.target).index());
                    });

                    element.on('mouseleave', startAutoSliding);
                    element.on('mouseenter', stopAutoSliding);
                    startAutoSliding();
                }
            }
        }
    }
}