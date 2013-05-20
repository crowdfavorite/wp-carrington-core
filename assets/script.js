(function($) {
	$(document).ready(function() {
		var	cfct_frames = [];
		$('body').on('click', 'a.js-cfct-delete-image', function(e) {
			e.preventDefault();
			$(this).parent('li').remove();
		});

		$('ul.js-cfct-images-multiple').sortable({
			placeholder: 'cfct-placeholder',
		});

		$( "ul.js-cfct-images-multiple").disableSelection();
		
		$('.js-cfct-select-image-single').click(function(e) {
			var $el = $(this);
			var name = $el.data('name');
			var $image  = $('ul.js-cfct-images-'+name);

			e.preventDefault();

			if (cfct_frames[name]) {
				cfct_frames[name].open();
				return;
			}

			cfct_frames[name] = wp.media.frames[name] = wp.media({
				title: $el.data('choose'),
				library: {
					type : 'image'
				},
				button : {
					// Don't close the modal, we'll handle that					
					close: false
				}
			});

			cfct_frames[name].open();

			cfct_frames[name].on('select', function() {
				// Grab the selected attachment.
				var attachment = cfct_frames[name].state().get('selection').first();
				// Ajax to get the thumbnail and input markup
				$.post( ajaxurl, {
					action: 'cfct_add_image_ajax',
					attachment_id: attachment.id,
					name : name,
					type : 'single'
				}).done( function(markup) {
					$image.html(markup);
					cfct_frames[name].close();
				});
			});
		});

		$('.js-cfct-select-image-multiple').click(function(e) {
			var $el = $(this);
			var name = $el.data('name');
			var $images = $('ul.js-cfct-images-'+name);

			e.preventDefault();

			if (cfct_frames[name]) {
				cfct_frames[name].open();
				return;
			}

			cfct_frames[name] = wp.media.frames[name] = wp.media({
				title: $el.data('choose'),
				library: {
					type : 'image'
				},
				button : {
					// Don't close the modal, we'll handle that					
					close: false
				}
			});

			cfct_frames[name].open();

			// When an image is selected, run a callback.
			cfct_frames[name].on('select', function() {
				// Grab the selected attachment.
				var attachment = cfct_frames[name].state().get('selection').first();
				// Ajax to get the thumbnail and input markup
				$.post( ajaxurl, {
					action: 'cfct_add_image_ajax',
					attachment_id: attachment.id,
					name : name,
					type : 'multiple'
				}).done( function(markup) {
					// This is a multiple selector, so we append the markup
					$images.append(markup);
					cfct_frames[name].close();
				});
			});
		});
	});
})(jQuery);