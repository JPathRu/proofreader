/**
 * Proofreader
 *
 * @package     Proofreader
 * @author      Sergey M. Litvinov (smart@joomlatune.com), Olga Novikova (helga@joomlatune.com)
 * @copyright   Copyright (C) 2013-2015 by Sergey M. Litvinov, Olga Novikova. All rights reserved.
 * @copyright   Copyright (C) 2005-2007 by Alexandr Balashov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

(function ($) {
	$.fn.proofreader = function (options, messages) {
		var $container = this,
			that = {
				initialized    : false,
				selectionObject: {},
				scripts        : []
			},
			pluginSettings = $.extend({
				'loadFormUrl'              : null,
				'messagesContainerSelector': '#proofreader_messages_container',
				'typoContainerSelector'    : '#proofreader_typo_container',
				'typoTextElementSelector'  : '#proofreader_typo_text',
				'typoPrefixElementSelector': '#proofreader_typo_prefix',
				'typoSuffixElementSelector': '#proofreader_typo_suffix',
				'highlightClass'           : 'proofreader_highlight',
				'messageErrorClass'        : 'proofreader_message_error',
				'overlayClass'             : 'proofreader_overlay',
				'popupClass'               : 'proofreader_popup',
				'popupCloseClass'          : 'proofreader_popup_close',
				'popupMessageClass'        : 'proofreader_popup_message',
				'popupMessageErrorClass'   : 'proofreader_popup_message_error',
				'popupMessageSuccessClass' : 'proofreader_popup_message_success',
				'popupDelay'               : 4000,
				'floatingButtonClass'      : 'proofreader_mouse',
				'floatingButtonOffset'     : 15,
				'floatingButtonDelay'      : 2000,
				'highlightTypos'           : false,
				'selectionMaxLength'       : 100,
				'showWordsBefore'          : 10,
				'showWordsAfter'           : 10,
				'handlerType'              : 'keyboard'
			}, options),
			l10n = $.extend({
				'reportTypo'           : 'Report a typo',
				'thankYou'             : 'Thank you for reporting the typo!',
				'browserIsNotSupported': 'Your browser does not support selection handling.',
				'selectionIsTooLarge'  : 'You have selected too large text block!'
			}, messages);

		that.init = function () {
			that.clearSelectionObject();

			if ($container.find('form').length) {
				$container.show();
				that.initForm();
			}

			if (pluginSettings.handlerType === 'keyboard'
				|| pluginSettings.handlerType === 'both') {
				that.addKeyboardEvents();
			}

			if (pluginSettings.handlerType === 'mouse'
				|| pluginSettings.handlerType === 'both') {
				that.addSelectionEvents();
			}

			that.wrapPopup($container, that.hideProofreader);
			that.createMessagePopup();
		};

		that.initForm = function () {
			that.$form = $container.find('form').first();
			if (that.$form.length) {
				that.$form
					.on('click', function (e) {
						return that.isSubmitButtonClick(e);
					})
					.on('submit', function (e) {
						that.submitForm();
						return false;
					});

				that.$messagesContainer = $(pluginSettings.messagesContainerSelector);
				that.$typoContainer = $(pluginSettings.typoContainerSelector);
				that.$typoTextElement = $(pluginSettings.typoTextElementSelector);
				that.$typoPrefixElement = $(pluginSettings.typoPrefixElementSelector);
				that.$typoSuffixElement = $(pluginSettings.typoSuffixElementSelector);
				that.$submitButton = that.$form.find('button[type="submit"],input[type="submit"]').first();

				that.initialized = true;
			}
		};

		that.loadForm = function (callback) {
			var url = pluginSettings.loadFormUrl,
				data = {
					'page_url'  : window.location.href,
					'page_title': $(document).find('title').text()
				};
			$.ajax({
				'type'    : 'GET',
				'url'     : url,
				'dataType': 'json',
				'data'    : data,
				'success' : function (data) {
					if (that.isValidFormResponse(data.form)) {
						that.replaceForm(data.form);
						that.injectScripts(data.scripts, data.script).done(function () {
							if (callback !== undefined) {
								callback();
							}
						});
					} else {
						that.hideProofreader();
					}
				},
				'error': function () {
					that.hideProofreader();
				}
			});
		};

		that.addKeyboardEvents = function () {
			var isCtrl = false;

			$(document)
				.keyup(function (e) {
					if (e.which === 17) {
						isCtrl = false;
					}
				})
				.keydown(function (e) {
					if (e.which === 27) {
						if ($container.is(':visible')) {
							that.hideProofreader();
						} else if (that.$messagePopup.is(':visible')) {
							that.resetMessagePopup();
						}

						return false;
					}

					if (e.which === 17) {
						isCtrl = true;
					}

					if (isCtrl === true && e.which === 13 && !$container.is(':visible')) {
						that.removeFloatingButton();
						that.refreshSelectionObject();
						that.showProofreader();

						return false;
					}
				});
		};

		that.addSelectionEvents = function () {
			$('body')
				.on('mouseup', function (e) {
					if (!that.isSubmitButtonClick(e)) {
						that.removeFloatingButton();
						that.refreshSelectionObject();

						if (that.canShowProofreader()) {
							that.createFloatingButton(e);
						}
					}
				});
		};

		that.injectScript = function (script) {
			$('head')
				.append($('<script>', {
					'type': 'text/javascript',
					'text': script
				}));
		};

		that.injectScripts = function (scripts, script) {
			var requests = [],
				d = $.Deferred();
			if (scripts) {
				$.each(scripts, function (index, src) {
					if ($.inArray(src, that.scripts) === -1 && !$('script[src="' + src + '"]').length) {
						requests.push($.ajax({
							'url'     : src,
							'dataType': 'script',
							'success' : function () {
								that.scripts.push(src);
							}
						}));
					}
				});

				if (script && script !== '') {
					if (requests.length) {
						$.when.apply($, requests).done(function () {
							that.injectScript(script);
							d.resolve();
						});
					} else {
						that.injectScript(script);
						d.resolve();
					}
				} else {
					d.resolve();
				}
			} else {
				d.resolve();
			}

			return d.promise();
		};

		that.canShowProofreader = function () {
			return !(that.selectionObject.text === '' || $container.is(':visible') || that.$messagePopup.is(':visible'));
		};

		that.showProofreader = function () {
			if (that.canShowProofreader()) {
				if (that.selectionObject.text.length > pluginSettings.selectionMaxLength) {
					that.showMessage(l10n.selectionIsTooLarge, pluginSettings.popupMessageErrorClass);

					return;
				}

				if (that.initialized) {
					that.showForm();
				} else if (pluginSettings.loadFormUrl) {
					that.loadForm(that.showForm);
				} else {
					return;
				}

				$container
					.parent()
					.show();
			}
		};

		that.hideProofreader = function () {
			that.clearSelectionObject();

			$container
				.parent()
				.hide();
		};

		that.showForm = function () {
			if (that.initialized) {
				that.$form.trigger('reset');
				that.removeFormMessages();
				that.renderFormTypoContainer();
				that.updateFormHiddenFields();

				if (that.$submitButton.length) {
					that.$submitButton.removeAttr('disabled');
				}
			}
		};

		that.submitForm = function () {
			var formData = that.$form.serialize(),
				typoText = that.selectionObject.text,
				$selectionNode = $(that.selectionObject.node);
			that.removeFormMessages();
			that.$submitButton.attr('disabled', 'disabled');

			$.ajax({
				'type'    : 'POST',
				'url'     : that.$form.attr('action'),
				'dataType': 'json',
				'data'    : formData,
				'success' : function (data) {
					if (data.error) {
						that.$submitButton.removeAttr('disabled');
						that.renderFormMessages(data.messages);
					} else {
						that.hideProofreader();
						if (that.isValidFormResponse(data.form)) {
							that.replaceForm(data.form);
							that.injectScripts(data.scripts, data.script);
						}
						if (pluginSettings.highlightTypos) {
							that.highlightTypo($selectionNode, typoText);
						}
						that.showMessage(l10n.thankYou, pluginSettings.popupMessageSuccessClass);
					}
				},
				'error'   : function () {
					that.hideProofreader();
				}
			});
		};

		that.replaceForm = function (form) {
			$container
				.find('form')
				.remove()
				.end()
				.show()
				.append(form);

			that.initForm();

			$.each(that.$form.find('label'), function(index, element){
				$(element).removeAttr('title');
			});

			that.$form
				.find('button')
				.focus();
		};

		that.isSubmitButtonClick = function (e) {
			return $(e.target).attr('type') === 'submit';
		};

		that.wrapPopup = function ($element, resetFunction) {
			var $overlay = $('<div>', {
					'class': pluginSettings.overlayClass
				})
				.on('click', function (e) {
					if (!that.isSubmitButtonClick(e)) {
						$(this).hide();
						resetFunction();
					}

					return true;
				}),
				$closeButton = $('<div>', {
					'class': pluginSettings.popupCloseClass
				})
				.on('click', resetFunction);

			$element
				.wrap($overlay)
				.prepend($closeButton);
		};

		that.createMessagePopup = function () {
			if (that.$messagePopup === undefined) {
				var $textContainer = $('<div>', {
					'class': pluginSettings.popupMessageClass
				});

				that.$messagePopup = $('<div>', {
						'class': pluginSettings.popupClass
					})
					.on('click', function () {
						return false;
					})
					.append($textContainer)
					.appendTo($('body'));
			}

			that.wrapPopup(that.$messagePopup, that.resetMessagePopup);
		};

		that.resetMessagePopup = function () {
			clearInterval(that.messagePopupTimer);
			that.$messagePopup
				.parent()
				.hide()
				.find('.' + pluginSettings.popupMessageClass)
				.attr('class', pluginSettings.popupMessageClass)
				.html('');
		};

		that.createFloatingButton = function (e) {
			var mousePos = that.getMousePosition(e);
			that.$floatingButton = $('<div>', {
					'text' : l10n.reportTypo,
					'class': pluginSettings.floatingButtonClass
				})
				.css({
					'position': 'absolute',
					'top'     : mousePos.y + pluginSettings.floatingButtonOffset + 'px',
					'left'    : mousePos.x + pluginSettings.floatingButtonOffset + 'px'
				})
				.on('mouseup', function (e) {
					that.showProofreader();
					$(this).remove();

					return false;
				})
				.appendTo($('body'));

			that.floatingButtonTimer = setTimeout(function () {
				if (that.$floatingButton !== undefined) {
					that.$floatingButton.remove();
				}
			}, pluginSettings.floatingButtonDelay);
		};

		that.removeFloatingButton = function () {
			if (that.floatingButtonTimer !== undefined) {
				clearInterval(that.floatingButtonTimer);
			}
			if (that.$floatingButton !== undefined) {
				that.$floatingButton.remove();
			}
		};

		that.updateFormHiddenFields = function () {
			if (that.$typoTextElement.length) {
				that.$typoTextElement.val(that.selectionObject.text);
			}

			if (that.$typoPrefixElement.length) {
				that.$typoPrefixElement.val(that.selectionObject.prefix);
			}

			if (that.$typoSuffixElement.length) {
				that.$typoSuffixElement.val(that.selectionObject.suffix);
			}
		};

		that.renderFormTypoContainer = function () {
			if (that.initialized && that.$typoContainer.length) {
				that.$typoContainer.html('');

				if (that.selectionObject.prefix !== '') {
					that.$typoContainer
						.append($('<span>', {
							'text': that.selectionObject.prefix
						}));
				}

				that.$typoContainer
					.append($('<span>', {
						'text' : that.selectionObject.text,
						'class': pluginSettings.highlightClass
					}));

				if (that.selectionObject.suffix !== '') {
					that.$typoContainer
						.append($('<span>', {
							'text': that.selectionObject.suffix
						}));
				}
			}
		};

		that.renderFormMessages = function (messages) {
			if (that.initialized && that.$messagesContainer.length) {
				that.removeFormMessages();
				$.each(messages, function (index, message) {
					that.$messagesContainer
						.append($('<div>', {
							'text' : message,
							'class': pluginSettings.messageErrorClass
						}));
				});
			}
		};

		that.removeFormMessages = function () {
			if (that.initialized && that.$messagesContainer.length) {
				that.$messagesContainer.html('');
			}
		};

		that.highlightTypo = function ($node, typoText) {
			var parts,
				text,
				pattern = '',
				replacement = '';

			if (typoText !== ''
				&& $node.length
				&& $node.prop('tagName').toLowerCase() !== 'body') {
				parts = typoText.split(' ');
				$.each(parts, function (index, text) {
					pattern = pattern
						+ '(<[^>]+>)?(\\s)?('
						+ text.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1')
						+ ')(\\s)?';
					replacement = replacement
						+ '$' + (index * 4 + 1)
						+ '$' + (index * 4 + 2)
						+ '<span class="' + pluginSettings.highlightClass + '">'
						+ '$' + (index * 4 + 3)
						+ '</span>'
						+ '$' + (index * 4 + 4);
				});

				text = $node
					.html()
					.replace(new RegExp(pattern, 'g'), replacement);
				if (text !== '') {
					$node.html(text);
				}
			}
		};

		that.showMessage = function (text, messageClass) {
			that.resetMessagePopup();
			that.removeFloatingButton();

			that.$messagePopup
				.parent()
				.show()
				.find('.' + pluginSettings.popupClass)
				.addClass(messageClass)
				.find('.' + pluginSettings.popupMessageClass)
				.text(text);

			that.messagePopupTimer = setTimeout(function () {
				that.resetMessagePopup();
			}, pluginSettings.popupDelay);
		};

		that.createSelectionObject = function (text, prefix, suffix, node) {
			return {
				'text'  : text,
				'prefix': prefix,
				'suffix': suffix,
				'node'  : node
			};
		};

		that.clearSelectionObject = function () {
			that.selectionObject = that.createSelectionObject('', '', '');
		};

		that.refreshSelectionObject = function () {
			if (window.getSelection) {
				that.selectionObject = that.getWebKitSelection();
			} else if (document.getSelection) {
				that.selectionObject = that.getGeckoSelection();
			} else if (document.selection) {
				that.selectionObject = that.getTridentSelection();
			} else {
				that.clearSelectionObject();
				that.showMessage(l10n.browserIsNotSupported, pluginSettings.popupMessageErrorClass);
			}
		};

		that.getRangeText = function (range) {
			var fragment = range.cloneContents(),
				div = document.createElement('div'),
				$skipElements,
				length;

			while (fragment.firstChild) {
				div.appendChild(fragment.firstChild);
			}

			$skipElements = $(div).find('script,style,form');
			length = $skipElements.length;
			while (length--) {
				$skipElements[length].parentNode.removeChild($skipElements[length]);
			}

			while (div.firstChild) {
				fragment.appendChild(div.firstChild);
			}

			return fragment.textContent;
		};

		that.getSelectionContainer = function (node) {
			while (node) {
				if (node.nodeType === 1) {
					return node;
				}
				node = node.parentNode;
			}
		};

		that.getWebKitSelection = function () {
			var selection = window.getSelection(),
				text = '',
				prefix = '',
				suffix = '',
				node,
				range,
				prefixRange,
				suffixRange;

			if (selection && selection.rangeCount > 0) {
				text = selection.toString();
				range = selection.getRangeAt(0);
				node = that.getSelectionContainer(range.commonAncestorContainer);

				prefixRange = range.cloneRange();
				prefixRange.setStartBefore(range.startContainer.ownerDocument.body);
				prefixRange.setEnd(range.startContainer, range.startOffset);
				prefix = that.truncateText(that.getRangeText(prefixRange), -pluginSettings.showWordsBefore);

				suffixRange = range.cloneRange();
				suffixRange.setStart(range.endContainer, range.endOffset);
				suffixRange.setEndAfter(range.endContainer.ownerDocument.body);
				suffix = that.truncateText(that.getRangeText(suffixRange), pluginSettings.showWordsAfter);
			}

			return that.createSelectionObject(text, prefix, suffix, node);

		};

		that.getGeckoSelection = function () {
			var text = document.getSelection().toString();

			// TODO try to get prefix and suffix
			return that.createSelectionObject(text, '', '');
		};

		that.getTridentSelection = function () {
			var selection = document.selection,
				range = selection.createRange,
				text = range.text,
				node = that.getSelectionContainer(range.parentElement()),
				prefixRange = selection.createRange(),
				suffixRange = selection.createRange();

			prefixRange.moveStart('word', -pluginSettings.showWordsBefore);
			prefixRange.moveEnd('character', -text.length);

			suffixRange.moveStart('character', text.length);
			suffixRange.moveEnd('word', pluginSettings.showWordsAfter);

			return that.createSelectionObject(text, prefixRange.text, suffixRange.text, node);
		};

		that.getMousePosition = function (e) {
			var pos = {'x': 0, 'y': 0};
			if (e) {
				if (e.pageX || e.pageY) {
					pos.x = e.pageX;
					pos.y = e.pageY;
				} else if (e.clientX || e.clientY) {
					pos.x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
					pos.y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
				}
			}
			return pos;
		};

		that.truncateText = function (text, length) {
			var words = text.replace(/(\r|\n|\t)+/g, ' ').replace(/(\s)+/g, ' ').split(' ').filter(Boolean),
				start = Math.min(words.length, Math.abs(length)),
				result = '';

			if (length > 0) {
				result = (text.match(/^\s/g) ? ' ' : '') + words.slice(0, start).join(' ');
			} else {
				result = words.slice(words.length - start).join(' ') + (text.match(/\s$/g) ? ' ' : '');
			}

			return result;
		};

		that.isValidFormResponse = function(form) {
			return form && $('<div>' + form + '</div>').find('form').length > 0;
		};

		that.init();

		return $container;
	};
})(jQuery);