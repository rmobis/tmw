$(function(){
	var charsTab = $('#chars'),
		addRow = charsTab.find('tr.add-row')
		addDiv = addRow.children('td'),
		slideText = addDiv.find('.slide-text'),
		errorModal = $('#error-modal'),
		errorMessage = errorModal.find('.modal-message');

	charsTab.on('click', '.icon-remove', function(e) {
		var row = $(this).closest('tr');
		e.preventDefault();

		var promise = $.ajax({
			url: document.location.href + '/remove',
			dataType: 'json',
			method: 'POST',
			data: {
				char: row.data('id')
			}
		});

		promise.done(function(data) {
			if (data.error === true) {
				errorMessage.text(data.message);
				errorModal.modal('show');
			} else {
				row.animate({opacity: 0}, function() {
					row.css('height', row.height());
					row.html('');
					row.slideUp(function() {
						row.remove();
					});
				});
			}
		});
	});

	addRow.on('click', function() {
		if (!slideText.attr('contenteditable')) {
			slideText.attr('contenteditable', true);

			// Select text
			if (document.body.createTextRange) { // IE
				range = document.body.createTextRange();
				range.moveToElementText(slideText[0]);
				range.select();
			} else if (window.getSelection) {
				selection = window.getSelection();
				range = document.createRange();
				range.selectNodeContents(slideText[0]);
				selection.removeAllRanges();
				selection.addRange(range);
			}
		}
	});

	slideText.on('focus', function() {
		addRow.addClass('hover');
	});

	slideText.on('blur', function() {
		addRow.removeClass('hover');
		slideText.removeAttr('contenteditable')
				 .text('New Character');
	});

	slideText.on('keydown', function(e) {
		if (e.keyCode === 13) { // Enter
			e.preventDefault();

			if (slideText.text() === 'New Character') {
				return;
			}

			var promise = $.ajax({
				url: document.location.href + '/add',
				dataType: 'json',
				method: 'POST',
				data: {
					char: slideText.text()
				}
			});

			promise.always(function(data) {
				if (data.error === true) {
					errorMessage.text(data.message);
					errorModal.modal('show');
				}

				slideText.trigger('blur');
			});
		} else if (e.keyCode === 27) { // Esc
			e.preventDefault();

			slideText.trigger('blur');
		}
	});
});