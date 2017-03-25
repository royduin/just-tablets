// Initialize after popover
function start_popover()
{
	// Clicked on remove items from comparison icon in popover?
	$('.icon-remove-sign').click(function()
	{
		var key = $(this).data('product-id');
		// Get from session storage
		object = JSON.parse(sessionStorage.getItem('compare'));
		// Remove current item
		delete object[key];
		// Save back to session storage
		sessionStorage.setItem('compare', JSON.stringify(object) );
		// Uncheck the current items checkbox
		$('.compare_item[value='+key+']').prop('checked',false);
		// Remove item from popover
		$('.popover li[data-product="'+key+'"]').slideUp(400,function()
		{
			// Align caption proper
			$('.popover').css('top', ( parseFloat($('.popover').css('top')) + 20.5 ) + 'px' );
			// Empty?
			if( $.isEmptyObject( object ) )
			{
				// Destroy!
				active_popover.popover('destroy');
			}
		});
	});
}


// Initialize popovers, tooltips and the comparison list functionalities
function start()
{
	// Popovers
	$("a[rel=popover]").popover({
		html: 		true,
		trigger: 	'hover',
		content: 	function(){
			return '<img src="'+$(this).data('img')+'" />';
		}
	});

	// Tooltips
	$("a[rel=tooltip]").tooltip().click(function(e){ e.preventDefault(); });
	$("a[rel=tooltip_clickable],a[class=tooltip_clickable]").tooltip();

	// Add/remove items to/from the compare items object and show popover
	$('.compare_item').click(function()
	{
		var cur = $(this);
		var id 	= cur.val();
		var tr 	= cur.parent().parent();

		// Something in sessionStorage?
		if(sessionStorage.length){
			object = JSON.parse(sessionStorage.getItem('compare'));
		} else {
			object = {};
		}

		// Add to the compare items object
		if(cur.attr('checked'))
		{
			object[id] 			= {};
			object[id]['name'] 	= tr.find('h2').text();
			object[id]['url'] 	= tr.find('h2 a').attr('href');
			object[id]['img'] 	= tr.find('img').attr('src');

			// TODO: Images doesn't work propper!!

		// Remove from the compare items object
		} else {
			delete object[id];
		}

		// Save to sessionStorage
		sessionStorage.setItem('compare', JSON.stringify(object) );

		// Set compare button href
		$('#compare_button').attr('href','http://justtablets.nl/tablets-vergelijken/'+($.isEmptyObject(object) ? '' : Object.keys(object).join('-')+'/' ));

		console.log( 'http://justtablets.nl/tablets-vergelijken/'+($.isEmptyObject(object) ? '' : Object.keys(object).join('-')+'/') );

		// If present, remove previous popover
		$('body').click();

		// Set active popover
		active_popover = cur;

		// Set and show popover
		cur.popover({
			title: 		'Producten in vergelijking <i class="icon-remove" title="Sluiten"></i>',
			html: 		true,
			placement: 	'left',
			trigger: 	'manual',
			content: 	function()
			{
				// Something stored in session storage?
				if( sessionStorage.length && ( ! $.isEmptyObject( JSON.parse( sessionStorage.getItem('compare') ) ) ) )
				{
					// Generate HTML for the popover
					html = '<ul class="unstyled compare-list">';
					$.each( JSON.parse( sessionStorage.getItem('compare') ) , function(key,value)
					{
						html += '<li data-product="'+key+'">';
						html += '<img src="'+value['img']+'" alt="'+value['name']+'" />';
						html += '<a href="'+value['url']+'">'+value['name']+'</a>';
						html += '<i class="icon-remove-sign" data-product-id="'+key+'" rel="tooltip" title="Product verwijderen uit vergelijking"></i>'
						html += '</li>';
					});
					html += '</ul><a href="'+$('#compare_button').attr('href')+'" class="btn btn-primary btn-small compare_button">Vergelijk</a>'
					return html;

				// Nothing stored in session storage?
				} else {
					return 'Er zijn (nog) geen producten in de vergelijking.'
				}
			}
		}).popover('show');
		start_popover();
	});
	
	// Remove popovers on click somewhere else
	$('body').click(function(e)
	{
		var target = e.target;

		// Active popover set?
		if( typeof(active_popover) != 'undefined' && active_popover != '')
		{
			// Not clicked on remove item from comparison icon in popover?
			if( $(target).attr('class') != 'icon-remove-sign' )
			{
				// Clicked on a compare checkbox?
				if( $(target).attr('class') == 'compare_item')
				{
					// Current is not equal to the active?
					if( target != active_popover[0] )
					{
						active_popover.popover('destroy');
						active_popover = '';
					}

				// Clicked somewhere else
				} else {
					active_popover.popover('destroy');
					active_popover = '';
				}
			}
		}
	});

	// Compare button clicked without selecting any products to compare
	$('#compare_button').click(function(e)
	{
		if( !sessionStorage.length || ( $.isEmptyObject( JSON.parse( sessionStorage.getItem('compare') ) ) ) )
		{
			alert('Je hebt geen producten aangevinkt om te vergelijken!')
			e.preventDefault();
		}
	});

	// Set compare button href, get the compare items object and check the present compare items
	if(sessionStorage.length)
	{
		// Get the compare items object
		var compare_items = JSON.parse( sessionStorage.getItem('compare') );

		//Set the compare button href
		if( ! $.isEmptyObject(compare_items) ){
			$('#compare_button').attr('href','http://justtablets.nl/tablets-vergelijken/'+Object.keys(compare_items).join('-')+'/');
		}

		// Check the present compare items
		$('.compare_item').each(function()
		{
			var cur = $(this);
			if( cur.val() in compare_items )
			{
				cur.attr('checked',true);
			}
		});
	}

	// Scroll to top
	$("#isonscreen a").click(function()
	{
		$("html, body").animate({scrollTop:0},"slow");
		return false;
	});

}

// Escape HTML
function escapeHtml(unsafe)
{
	return unsafe
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}

// Detect touch devices
function is_touch_device()
{
	return false;
	return !!document.createTouch;
	return !!('ontouchstart' in window) || !!('onmsgesturechange' in window);
};

// Detect url parts
function getQueryParams(qs) {
	if(qs)
	{
		qs = qs.split("+").join(" ");
		var params = {},
			tokens,
			re = /[?&]?([^=]+)=([^&]*)/g;

		while (tokens = re.exec(qs)) {
			params[decodeURIComponent(tokens[1])]
				= decodeURIComponent(tokens[2]);
		}

		return params;
	}
	return false;
}

// Check if a element is on screen
// http://upshots.org/javascript/jquery-test-if-element-is-in-viewport-visible-on-screen
$.fn.isOnScreen = function(){
	 
	var win = $(window);
	 
	var viewport = {
		top : win.scrollTop(),
		left : win.scrollLeft()
	};
	viewport.right = viewport.left + win.width();
	viewport.bottom = viewport.top + win.height();
	 
	var bounds = this.offset();
	bounds.right = bounds.left + this.outerWidth();
	bounds.bottom = bounds.top + this.outerHeight();
	 
	return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));	 
};

//Run! :)
(function()
{
	start();

	// Show/hide all extra filter options
	$('.toggler').click(function(e)
	{
		var upper_this = $(this);
		$('.toggle_' + $(this).data('to') ).slideToggle(100,function()
		{
			if( $(this).is(":hidden") ){
				upper_this.html('<i class="icon-chevron-down"></i> Alle opties');
			} else {
				upper_this.html('<i class="icon-chevron-up"></i> Minder opties');
			}
		});
		e.preventDefault();
	});

	// Toggler for non default filters	
	$('.notdefault_title').click(function()
	{
		$(this).next().slideToggle(100);
	});


	// Sliders at non-touch devices
	if(is_touch_device() == false)
	{
		$('.filter_select').each(function()
		{
			$('#' + $(this).children('select').eq(0).attr('id') + ', #' + $(this).children('select').eq(1).attr('id') + '').selectToUISlider({
				labels: 		3,
				labelSrc: 		'text',
				sliderOptions: 	{
					stop: 	function(e,ui){
						var thisHandle = $(ui.handle);
						var currSelect = $('#' + thisHandle.attr('id').split('handle_')[1]);
						currSelect.change();
					}
				}
			}).hide().prev('label').hide();
		});
	}

	// Fancybox on tablet page
	if(window.location.pathname.search('tablet/') != -1)
	{
		$(".fancybox").fancybox({
			helpers: {
				thumbs: {
					width: 75,
					height: 75
				}
			}
		});
	}

	// Tablet compare or wizard page
	if(window.location.pathname.search('tablets-vergelijken/') != -1 || window.location.pathname.search('tablet-kiezen/') != -1)
	{
		// Filters
		$('input[type=checkbox]:not(.compare_item),select').change(function()
		{
			// Set object
			var ajaxData 	= {};

			// Set CSRF token
			ajaxData['justtablets_csrf'] = $('input[name=justtablets_csrf]').val();

			// Buyable checked?
			if( $('#buyable:checked').length ){
				ajaxData['buyable'] = true;
			}

			// Set price from/to
			ajaxData['price_from'] 	= $('#price_from').val();
			ajaxData['price_to'] 	= $('#price_to').val();

			// Set specification filters
			ajaxData['spec1']		= $('#spec1').val();
			ajaxData['spec2']		= $('#spec2').val();
			ajaxData['spec3']		= $('#spec3').val();

			// Set current order
			var sortinfo = getQueryParams($('#sorting li a.current').attr('href'));

			// Something set?
			if(sortinfo)
			{
				// Sort option
				ajaxData['sort'] 			= sortinfo['http://justtablets.nl/tablets-vergelijken/?sort'];

				// Toggle asc/desc because the link is to toggle it
				ajaxData['sort_dir']		= (sortinfo['sort_dir'] == 'asc' ? 'desc' : 'asc');
				
				// Sort_spec present?
				if(typeof sortinfo['sort_spec'] !== "undefined" && sortinfo['sort_spec'])
				{
					ajaxData['sort_spec']	= sortinfo['sort_spec'];
				}
			}

			// Loop trough filters options
			$('input[type=checkbox]:not(.compare_item):not(#buyable):checked,select:not(#price_from):not(#price_to):not(#spec1):not(#spec2):not(#spec3)').each(function()
			{
				// Feature not already present?
				if( typeof ajaxData[ $(this).attr('name') ] == 'undefined')
				{
					// Create array
					ajaxData[ $(this).attr('name') ] = [];
				}

				// Add value to the array
				ajaxData[ $(this).attr('name') ].push( $(this).val() );
			});

			// Cancel current ajax calls
			if ( typeof call !== "undefined" && call) {
				call.abort();
			}

			// Tablet choise wizard?
			if(window.location.pathname.search('tablet-kiezen') != -1)
			{
				call = $.ajax({
					url: 		'http://justtablets.nl/ajax/filter/0/true',
					data: 		ajaxData,
					dataType: 	'text',
					type: 		'POST',
					error: function(jqXHR, textStatus, errorThrown)
					{
						alert('Er ging iets fout! ' + errorThrown);
					},
					success: function(hash)
					{
						// Just set the hash in the button href
						$('#gowizard').attr('href','http://justtablets.nl/tablets-vergelijken#filter:' + hash);
					}
				});
				return false;
			}

			// Remove no results message
			$('.no-results').remove();

			// If present, remove goto top message
			$('#isonscreen').remove();

			// Show loader
			$('.loader').remove();
			$('.span10').hide().after('<div class="loader"></div>');

			// Set url for ajax call
			var url = 'http://justtablets.nl/ajax/filter/';

			//First run?
			if(first_run == true)
			{
				// Set filter hash
				url += window.location.hash.replace('#filter:','');
				first_run = false;
			}

			// Call 911! Ow no, just Ajax :)
			call = $.ajax({
				url: 		url,
				data: 		ajaxData,
				dataType: 	'json',
				type: 		'POST',
				error: function(jqXHR, textStatus, errorThrown)
				{
					// Only show errors when it's not aborted
					// (in case a filter is going to run twice,
					// the first one will be aborted)
					if(errorThrown != 'abort')
					{
						// Errorrrrrrrrrrrrrrr! :(
						alert('Er ging iets fout! ' + errorThrown);
					}
				},
				success: function(data)
				{
					// Uncheck buyable (will be checked if it was checked later, keep reading!)
					$('#buyable').attr('checked', false);

					// Clear the tables body
					$('tbody').empty();

					// Hide pagination
					$('.pagination').hide();

					// Set products array
					var products = [];

					// Foreach received data
					$.each(data,function(key,value)
					{
						// Hash?
						if(key == 'hash')
						{
							// Replace old hash to new hash in sorting option urls
							$.each( $('.sort') , function(k,v)
							{
								$(v).attr('href',$(v).attr('href').replace('&filter='+window.location.hash.replace('#filter:',''),'') + '&filter=' + value );
							});

							// Set hash in url
							window.location.hash = 'filter:'+value;

							// Make hash a global value
							window[key] = value;

						// Input returend (if the url will be copied, everything will still checked and stuff)
						} else if(key == 'input')
						{
							// Foreach input data
							$.each(value,function(i,input)
							{
								// Brand
								if(i == 'brands')
								{
									// Foreach brand
									$.each(input,function(b,brand)
									{
										// Check the selected brand
										$('#b'+brand).attr('checked', true);

										// Open toggle brands if brand is in toggler
										$('#b'+brand).closest('.toggle_brands').show();
									});

								// Price from/to
								} else if(i == 'price_from' || i == 'price_to')
								{
									// Check the selected price option
									$('select#'+i+' option[value='+input+']').attr('selected', 'selected').click();

								// Buyable
								} else if(i == 'buyable')
								{
									// If selected, check the buyable checkbox
									$('#buyable').attr('checked', true);

								// Currect sorting
								} else if(i == 'sort')
								{
									// Foreach sorting options
									$.each( $('#sorting a') , function(s,so)
									{
										// Remove possible current classes
										$(this).removeClass('current asc desc');

										// Sorting by specification?
										if(input == 'spec')
										{
											// Add specification id
											input = input + value['sort_spec'];
										}

										// Current is equal to the selected?
										if( 'order_' + input == $(so).attr('id') )
										{
											// Set class
											$(so).addClass('current');
										}
									});

								// Sorting direction
								} else if(i == 'sort_dir')
								{
									// Foreach sorting options
									$.each( $('#sorting a') , function(s,so)
									{
										// Current is equal to the selected?
										if( $('#sorting a.current').attr('id') == $(so).attr('id') )
										{
											// Set class (asc or desc)
											$('#sorting a.current').addClass(input);
										}

										// Toggle asc/desc in urls
										$(this).attr('href', $(this).attr('href').replace(input,'asc').replace(input,'desc') );
									});

								// Specification filter
								} else if(i.search('spec') != -1)
								{
									// De-select default
									$('#'+i+' option:selected').removeAttr('selected');

									// Select selected
									$('#'+i+' option[value='+input+']').attr('selected','selected');

									// Change sorting options to the selected specification filter options
									$('#order_'+i).html( $('#'+i+' option:selected').text() );

								// Option filters?
								} else if(i.search('o') != -1)
								{
									// Set the selected options filter and open filter block
									$('select#'+i+'f option[value="'+input[0]+'"]').attr('selected','selected').click();
									$('select#'+i+'t option[value="'+input[1]+'"]').attr('selected','selected').click().closest('.notdefault_div').show();

								// Checkbox filters?
								} else if(i.search('f') != -1)
								{
									// Foreach checkbox filter "group"
									$.each(input,function(f,filter)
									{
										// Set the selected checkbox filter
										var current = $('#'+i+'_'+filter).attr('checked', true);

										// Open "more options" if filter is in toggler
										current.closest('.hide').show();

										// Open filter block
										current.closest('.notdefault_div').show();
									});
								}
							});

						// Pagination information
						} else if(key == 'total_amount' || key == 'total_pages' || key == 'current_page')
						{
							// Set the text for the html elements
							$('#' + key).html(value);

							// Make the information a global value
							window[key] = value;

						// Pagination
						} else if(key == 'pagination')
						{
							$('.pagination').after(value).remove();
							$.each( $('.pagination a') , function(k,v)
							{
								if( $(v).attr('href') != '#' )
								{
									$(v).attr('href', $(v).attr('href') + '&filter=' + hash );
								}
							});

						// Yeah, finally! Products!
						} else
						{
							// Comming from the tablet choise wizard?
							if(document.referrer.search('tablet-kiezen') != -1){
								//TODO: First 3 best choise. Label with gold, silver and bronse!
							}
							// Set product urls
							value.url = 'http://justtablets.nl/tablet/'+value.product_id+'/'+value.title_url;
							// Escape title html
							value.title = escapeHtml(value.title);
							// Push in the the products array
							products.push(value);
						}
					});

					// Send data to the template and append it in the tables body
					$('#productTemplate').tmpl(products).appendTo('tbody');

					// Remove the loader gif
					$('.loader').remove();

					// No products?
					if(total_amount == 0)
					{
						// Show a "no products" message
						$('.span10').after('<div class="span10 no-results"><div class="alert alert-error">Er zijn geen producten gevonden die voldoen aan de gekozen filters.</div></div>');
					} else {
						// Show the results!
						$('.span10').show();
					}

					// Goto top message
					if( ! $('.span10:visible').isOnScreen() )
					{
						// Calculate position
						var top 	= $(document).scrollTop() + ($(window).height() / 3);
						var left 	= ($('.span10').width() / 2) - 225;

						// No results?
						if(total_amount == 0)
						{
							var message = 'Er zijn <strong>geen tablets gevonden</strong>.';

						// One result?
						} else if(total_amount == 1)
						{
							var message = 'Er is <strong>één tablet gevonden</strong>. Klik hier om deze te bekijken.';

						// Multiple results?
						} else {
							var message = 'Er zijn <strong>'+total_amount+' tablets gevonden</strong>. Klik hier om deze te bekijken.'; 
						}
						
						$('.span10').append('<div id="isonscreen" style="top: '+top+'px; margin-left: '+left+'px;"><a href="#">'+message+'</a></div>');
					}

					// Restart Javascript functions so tooltips and stuff will work
					start();

					// Just for debugging!
					// $('#debugger').remove();
					// $('.navbar').after('<iframe id="debugger" src="http://justtablets.nl/ajax/filter/'+hash+'"></iframe>');
				}
			});

		});

	}

	// Filters present?
	if(window.location.hash)
	{
		var first_run = true;

		// Execute the ajax call directly with the given filter hash
		$('input[type=checkbox]').first().change();
	}

})();
// That's it for now!