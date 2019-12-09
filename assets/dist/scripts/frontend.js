var TotalContest;
(function (TotalContest) {
    jQuery['validator'].addMethod('minfilesize', function (value, element, params) {
        return this.optional(element) || (params[1] && params[1] < (element.files[0].size / 1024));
    });
    jQuery['validator'].addMethod('maxfilesize', function (value, element, params) {
        return this.optional(element) || (params[1] && params[1] > (element.files[0].size / 1024));
    });
    jQuery['validator'].addMethod('formats', function (value, element, params) {
        return this.optional(element) || (params[1] && params[1].indexOf(element.files[0].type) !== -1);
    });
    jQuery(function () {
        var messages = {};
        jQuery.each(window['jqValidationMessages'], function (rule, message) {
            messages[rule] = function (validation, element) {
                return jQuery['validator'].format(message
                    .replace('{{label}}', element.type === 'file' ? '' : jQuery(element).siblings('label').text())
                    .replace('%d', '{0}')
                    .replace('%s', '{0}'), validation);
            };
        });
        jQuery.extend(jQuery['validator'].messages, messages);
    });
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var EmbedResizingBehaviour = /** @class */ (function () {
        function EmbedResizingBehaviour(contest) {
            var _this = this;
            this.contest = contest;
            this.listener = function (event) { return _this.receiveRequest(event); };
            window.addEventListener("message", this.listener, false);
        }
        EmbedResizingBehaviour.prototype.destroy = function () {
            window.removeEventListener("message", this.listener);
        };
        EmbedResizingBehaviour.prototype.postHeight = function () {
            top.postMessage({ totalcontest: { id: this.contest.id, action: 'resizeHeight', value: jQuery(document.body).height() } }, '*');
        };
        EmbedResizingBehaviour.prototype.receiveRequest = function (event) {
            if (event.data.totalcontest && event.data.totalcontest.id === this.contest.id && event.data.totalcontest.action === 'requestHeight') {
                this.postHeight();
            }
        };
        return EmbedResizingBehaviour;
    }());
    TotalContest.EmbedResizingBehaviour = EmbedResizingBehaviour;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var AjaxBehaviour = /** @class */ (function () {
        function AjaxBehaviour(contest) {
            var _this = this;
            this.contest = contest;
            this.contest.on('state', function (event, state) {
                if (state && state.ajax) {
                    _this.load(state.ajax);
                }
            });
            contest.element.find('a[totalcontest-ajax-url]').on('click', function (event) {
                var ajaxUrl = jQuery(event.currentTarget).attr('totalcontest-ajax-url');
                if (ajaxUrl) {
                    window.history.pushState({ ajax: ajaxUrl }, null, event.currentTarget.href);
                    _this.load(ajaxUrl);
                }
                event.preventDefault();
            });
            contest.element.find('option[totalcontest-ajax-url]').closest('select').on('change', function (event) {
                var selected = jQuery(event.currentTarget).find('option:selected');
                var ajaxUrl = selected.attr('totalcontest-ajax-url');
                if (ajaxUrl) {
                    window.history.pushState({ ajax: ajaxUrl }, null, selected.val());
                    _this.load(ajaxUrl);
                }
                event.preventDefault();
            });
            contest.element.find('form').on('submit', function (event) {
                event.preventDefault();
                if (!jQuery.fn.valid || jQuery(event.currentTarget).valid()) {
                    _this.load(contest.config.ajaxEndpoint, new FormData(event.currentTarget), 'POST');
                }
            });
        }
        AjaxBehaviour.prototype.load = function (url, data, method) {
            var _this = this;
            if (data === void 0) { data = {}; }
            if (method === void 0) { method = 'GET'; }
            this.contest.element.css('pointer-events', 'none').fadeTo(500, 0.1, function () {
                jQuery.ajax({
                    url: url,
                    data: data,
                    processData: method == 'POST' ? false : true,
                    contentType: false,
                    type: method,
                    success: function (response) {
                        var $contest = jQuery(response).hide();
                        _this.contest.element.replaceWith($contest);
                        $contest.fadeIn();
                        new TotalContest.Contest($contest, true);
                    }
                });
            });
        };
        return AjaxBehaviour;
    }());
    TotalContest.AjaxBehaviour = AjaxBehaviour;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var UploadedFile = /** @class */ (function () {
        function UploadedFile(originalFile) {
            this.originalFile = originalFile;
            this.URL = window.URL || window['webkitURL'];
        }
        UploadedFile.prototype.getDimensions = function (callback) {
            var _this = this;
            var image = document.createElement('img');
            image.onload = function (event) {
                _this.originalFile.dimensions = image.width + 'x' + image.height;
                callback(_this.originalFile.dimensions);
            };
            image.src = this.getObjectURL();
        };
        UploadedFile.prototype.getDuration = function (callback) {
            var _this = this;
            var media = document.createElement(this.getType());
            media.src = this.getObjectURL();
            media.ondurationchange = function () {
                _this.originalFile.duration = new Date(media['duration'] * 1024).toISOString().substr(14, 5);
                callback(_this.originalFile.duration);
            };
        };
        UploadedFile.prototype.getObjectURL = function () {
            return URL.createObjectURL(this.originalFile);
        };
        UploadedFile.prototype.getPreview = function (callback) {
            var _this = this;
            if (this.getType() === 'image') {
                this.getDimensions(function () { return callback(_this.originalFile.name + " - " + _this.originalFile.dimensions + " (" + _this.getSize() + ")"); });
            }
            else {
                this.getDuration(function () { return callback(_this.originalFile.name + " - " + _this.originalFile.duration + " (" + _this.getSize() + ")"); });
            }
        };
        UploadedFile.prototype.getSize = function () {
            return TotalContest.Utils.getFileSizeForHuman(this.originalFile.size);
        };
        UploadedFile.prototype.getType = function () {
            return this.originalFile.type.split('/')[0];
        };
        return UploadedFile;
    }());
    TotalContest.UploadedFile = UploadedFile;
    var MaxLengthValidation = /** @class */ (function () {
        function MaxLengthValidation(element) {
            var _this = this;
            this.element = element;
            this.maxlength = 0;
            this.maxlength = element.attr('maxlength');
            this.counter = jQuery("<div style=\"float: right;margin-top: 0.5em;\"></div>");
            element.after(this.counter);
            element.on('change keypress keyup keydown', function () { return _this.updateCounter(); });
            this.updateCounter();
        }
        MaxLengthValidation.prototype.updateCounter = function () {
            this.counter.text(window['jqValidationMessages']['left'].replace('%d', this.maxlength - this.element.val().length));
        };
        return MaxLengthValidation;
    }());
    TotalContest.MaxLengthValidation = MaxLengthValidation;
    var SubmissionFormBehaviour = /** @class */ (function () {
        function SubmissionFormBehaviour(contest) {
            var _this = this;
            this.contest = contest;
            this.formElement = contest.element.find('.totalcontest-participate-form');
            this.formLoading = contest.element.find('.totalcontest-form-loading');
            this.fileInputsElements = contest.element.find('.totalcontest-form-field-type-file');
            this.formElement.validate({
                submitHandler: function (form, event) {
                    _this.showProgress(form, event);
                    if (!_this.contest.config['behaviours']['ajax']) {
                        form.submit();
                    }
                },
                onclick: true,
                errorPlacement: function (error, element) {
                    element.siblings(".totalcontest-form-field-errors").html('');
                    error.appendTo(element.siblings(".totalcontest-form-field-errors"));
                }
            });
            this.formElement.attr('novalidate', 'novalidate');
            this.fileInputsElements.each(function (index, element) {
                element = jQuery(element);
                var fileInputElement = element.find('input');
                var fileLabelElement = element.find('label');
                fileInputElement.on('change', function (event) { return _this.handleFile(event, fileInputElement, fileLabelElement); });
                fileLabelElement.data('original-text', fileLabelElement.text());
            });
            contest.element.find('[maxlength]:not([type="file"])').each(function () {
                new MaxLengthValidation(jQuery(this));
            });
            contest.element.find('[maxlength][type="file"],[minlength][type="file"]').each(function () {
                var $field = jQuery(this);
                var rules = {};
                var minLength = Number($field.attr('minlength') || 0);
                var maxLength = Number($field.attr('maxlength') || 0);
                if (minLength > 0) {
                    rules.minfilesize = [TotalContest.Utils.getFileSizeForHuman(minLength * 1024), minLength];
                }
                if (maxLength > 0) {
                    rules.maxfilesize = [TotalContest.Utils.getFileSizeForHuman(maxLength * 1024), maxLength];
                }
                $field.rules('add', rules);
                $field.rules('remove', 'minlength maxlength');
                $field.removeAttr('minlength');
                $field.removeAttr('maxlength');
            });
            contest.element.find('[formats][type="file"]').each(function () {
                var $field = jQuery(this);
                $field.rules('add', {
                    formats: [$field.attr('formats'), $field.attr('accept').split(',')]
                });
            });
            contest.element.find('[required-if-empty]').each(function () {
                var $field = jQuery(this);
                var $remoteField = jQuery($field.attr('required-if-empty'));
                $remoteField.on('change', function () {
                    $field.prop('required', !Boolean($remoteField.val()));
                });
            });
        }
        SubmissionFormBehaviour.prototype.handleFile = function (event, input, label) {
            if (event.target.files[0]) {
                var file = new UploadedFile(event.target.files[0]);
                file.getPreview(function (content) { return label.text(content); });
            }
            else {
                label.text(label.data('original-text'));
            }
            input.blur();
        };
        SubmissionFormBehaviour.prototype.showProgress = function (form, event) {
            var _this = this;
            this.formElement.find('[type="submit"]').prop('disabled', true);
            this.formLoading.addClass('active');
            jQuery('body').animate({ scrollTop: this.formLoading.find('svg').offset().top - 250 });
            setTimeout(function () { return _this.contest.element.finish().fadeTo(0, 1); }, 1);
        };
        return SubmissionFormBehaviour;
    }());
    TotalContest.SubmissionFormBehaviour = SubmissionFormBehaviour;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var SubmissionsListingBehaviour = /** @class */ (function () {
        function SubmissionsListingBehaviour(contest) {
            var _this = this;
            this.contest = contest;
            this.submissions = contest.element.find('[totalcontest-submissions]');
            this.layoutTogglers = contest.element.find('[totalcontest-submissions-toggle-layout]');
            this.layoutTogglers.on('click', function (event) { return _this.toggleLayout(event.currentTarget.getAttribute('totalcontest-submissions-toggle-layout')); });
        }
        SubmissionsListingBehaviour.prototype.toggleLayout = function (layout) {
            this.layoutTogglers.removeClass('totalcontest-submissions-toolbar-active');
            this.layoutTogglers.filter("[totalcontest-submissions-toggle-layout=\"" + layout + "\"]").addClass('totalcontest-submissions-toolbar-active');
            this.submissions.removeClass('totalcontest-submissions-items-layout-grid totalcontest-submissions-items-layout-list');
            this.submissions.addClass("totalcontest-submissions-items-layout-" + layout);
        };
        return SubmissionsListingBehaviour;
    }());
    TotalContest.SubmissionsListingBehaviour = SubmissionsListingBehaviour;
})(TotalContest || (TotalContest = {}));
var TotalContest;
(function (TotalContest) {
    var ScrollOverflowBehaviour = /** @class */ (function () {
        function ScrollOverflowBehaviour(contest) {
            var _this = this;
            this.contest = contest;
            this.scrollables = contest.element.find('[totalcontest-mobile-scrollable]');
            this.scrollables.on('scroll', function (event) { return _this.receiveScroll(event); });
            this.scrollables.each(function (index, scrollable) {
                var $scrollable = jQuery(scrollable);
                $scrollable.scrollLeft($scrollable.find('.is-active').first().offset().left);
            });
        }
        ScrollOverflowBehaviour.prototype.destroy = function () {
            this.scrollables.off('scroll');
        };
        ScrollOverflowBehaviour.prototype.receiveScroll = function (event) {
            var $target = jQuery(event.target);
            var maxWidth = $target.prop('scrollWidth') - $target.prop('clientWidth');
            if (Math.round($target.scrollLeft()) >= maxWidth) {
                $target.parent().addClass('is-scroll-finished');
            }
            else {
                $target.parent().removeClass('is-scroll-finished');
            }
        };
        return ScrollOverflowBehaviour;
    }());
    TotalContest.ScrollOverflowBehaviour = ScrollOverflowBehaviour;
})(TotalContest || (TotalContest = {}));
///<reference path="../../../../build/typings/index.d.ts" />
///<reference path="validations.ts" />
///<reference path="behaviours/embed.ts" />
///<reference path="behaviours/ajax.ts" />
///<reference path="behaviours/submission-form.ts" />
///<reference path="behaviours/submissions-listing.ts" />
///<reference path="behaviours/scroll-overflow.ts" />
var TotalContest;
(function (TotalContest) {
    TotalContest.Contests = {};
    var Contest = /** @class */ (function () {
        function Contest(element, viaAjax) {
            if (viaAjax === void 0) { viaAjax = false; }
            this.element = element;
            this.viaAjax = viaAjax;
            this.behaviours = {};
            this.config = {};
            this.id = element.attr('totalcontest');
            this.config = JSON.parse(element.find('[totalcontest-config]').text());
            this.screen = element.attr('totalcontest-screen');
            if (TotalContest.Contests[this.id]) {
                // Destroy the old instance
                TotalContest.Contests[this.id].destroy();
                element.fadeIn();
            }
            // Save instance for future usage
            TotalContest.Contests[this.id] = this;
            element.data('contest', TotalContest.Contests[this.id]);
            if (this.screen === 'contest.participate') {
                // Submission form
                this.behaviours['submissionForm'] = new TotalContest.SubmissionFormBehaviour(this);
            }
            else if (this.screen == 'contest.submissions') {
                // Submission listing
                this.behaviours['submissionsListing'] = new TotalContest.SubmissionsListingBehaviour(this);
            }
            // Ajax
            if (this.config['behaviours']['async'] || this.config['behaviours']['ajax']) {
                this.behaviours['ajax'] = new TotalContest.AjaxBehaviour(this);
            }
            // Async
            if (this.config['behaviours']['async']) {
                this.behaviours['ajax'].load(element.attr('totalcontest-ajax-url'));
            }
            // Embed resizing
            if (window.top !== window.self) {
                this.behaviours['embed'] = new TotalContest.EmbedResizingBehaviour(this);
                this.behaviours['embed'].postHeight();
            }
            // Scroll
            // this.behaviours['scrollOverflow'] = new ScrollOverflowBehaviour(this);
        }
        Contest.prototype.destroy = function () {
            jQuery.each(this.behaviours, function (id, behaviour) {
                if (behaviour.destroy) {
                    behaviour.destroy();
                }
            });
            this.element.remove();
        };
        Contest.prototype.isViaAjax = function () {
            return this.viaAjax;
        };
        Contest.prototype.off = function (event, callback) {
            this.element.off(event, callback);
        };
        Contest.prototype.on = function (event, callback) {
            this.element.on(event, callback);
        };
        return Contest;
    }());
    TotalContest.Contest = Contest;
    var Utils = /** @class */ (function () {
        function Utils() {
        }
        Utils.getFileSizeForHuman = function (sizeInBytes) {
            return ((sizeInBytes / Math.pow(1024, Math.floor(Math.log(sizeInBytes) / Math.log(1024)))) || 0).toFixed(2) + ' ' + ' KMGTP'.charAt(Math.floor(Math.log(sizeInBytes) / Math.log(1024))) + 'B';
        };
        Utils.getUrlParameters = function (url) {
            var params = {};
            window['decodeURIComponent'](url).replace(/[?&]+([^=&]+)=([^&]*)/gi, function (search, key, value) {
                params[key] = value;
            });
            return params;
        };
        ;
        Utils.refreshTinyMCE = function () {
            if (window['tinymce']) {
                setTimeout(function () {
                    for (var id in window['tinyMCEPreInit'].mceInit) {
                        window['tinymce'].remove();
                        var init = window['tinyMCEPreInit'].mceInit[id];
                        var $wrap = window['tinymce'].$("#wp-" + id + "-wrap");
                        if (($wrap.hasClass('tmce-active') || !window['tinyMCEPreInit'].qtInit.hasOwnProperty(id)) && !init.wp_skip_init) {
                            window['tinymce'].init(init);
                        }
                    }
                }, 100);
            }
        };
        return Utils;
    }());
    TotalContest.Utils = Utils;
    jQuery(function ($) {
        $('[totalcontest]').each(function () {
            new TotalContest.Contest($(this));
        });
        jQuery(window).on('popstate', function (event) {
            $('[totalcontest]').triggerHandler('state', [event.originalEvent.state]);
            return false;
        });
    });
})(TotalContest || (TotalContest = {}));

//# sourceMappingURL=maps/frontend.js.map
