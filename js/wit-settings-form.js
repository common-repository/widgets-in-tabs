/*! Widgets In Tabs */

jQuery(function($){
	$('.wit-area-remove').click(function(){
		$(this).closest('li.wit-area').remove();
	});

	$('.wit-area-edit').click(function(){
		$(this).siblings('.wit-area-name').trigger('click');
	});

	$('#wit-add-new').click(function(){
		var areaNameEl = $('.wit-area-template')
			.last()
			.clone(true)
			.removeClass('wit-area-template')
			.addClass('wit-area')
			.appendTo($("#wit-area-list"))
			.show()
			.find('.wit-area-name');
		areaNameEl.text(areaNameEl.text() + "-" + $("#wit-area-list li").length);
	});

	$('.wit-area-name').click(function(){
		$(this).siblings('input').val($(this).text());
		$(this).hide();
		$(this).siblings('input').show().focus().select();
	});

	$('.wit-area input').focusout(function(){
		$(this).hide();
		$(this).siblings('.wit-area-name').show();
	});

	$('.wit-area input').keyup(function(e){
		if (e.keyCode == 13) {
			$(this).siblings('.wit-area-name').text($(this).val());
			$(this).hide();
			$(this).siblings('.wit-area-name').show();
		}
  		if (e.keyCode == 27) {
  			$(this).hide();
			$(this).siblings('.wit-area-name').show();
  		}
		
	});

	$('#wit-form').submit(function(e){
		/* check that all areas have different names */
		var $areas = $('.wit-area:visible');
		var names = [];
		var name = "";
		var allGood = true;
		$areas.each(function(index, val){
			name = $(val).find('.wit-area-name').text();
			if (name.indexOf('+') >= 0 || $.inArray(name,names) >= 0) {
				allGood = false;
			}
			names.push(name);
		});
		
		if (allGood) {
			$('.wit-error').hide();
		} else {
			$('.wit-success').hide();
			$('.wit-error').show();
			return false;
		}

		/* update hidden input value */
		$(this).find('input[name=wit_areas]').val(names.join('+'));

		/* submit */
		return true;
	})
});