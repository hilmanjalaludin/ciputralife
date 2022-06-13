(function($){
$.fn.aqTip = function(html,options) {
	var opts = $.extend({}, $.fn.aqTip.defaults, options);

	return this.each(function() {
		var $obj = $(this);

		$('<div class="aqTip"><\/div>').appendTo($obj);

		var $layer = $('.aqTip',$obj);

		$layer.css({ display: 'none', position: 'absolute' }).css(opts.css);

		if (jQuery.isFunction(html)) html($layer);
		else $layer.html(html);

		var p = $obj.position();
		var ow = $obj.width() > $layer.width() 
			? $obj.width() : $layer.width();
		var x = p.left + ow + opts.marginX;
		if (x > document.body.clientWidth)
			x = p.left - ow - opts.marginX;

		$layer.css({ left: x+'px', top: p.top+opts.marginY+'px' });

		$obj.hover(function(){$layer.show()}, function(){$layer.hide()});
	});
};

$.fn.aqTipOne = function(html,options) {
	var opts = $.extend({}, $.fn.aqTip.defaults, options);
	return this.each(function() {
		if (!$('#aqTip').length) {
			$('<div id="aqTip"><\/div>').appendTo(document.body);
			$('#aqTip').css({ display: 'none', position: 'absolute' })
				.css(opts.css);
		}

		var $obj = $(this);
		if (html) {
			$('#aqTip').html(html);

			var p = $obj.position();
			var ow = $obj.width() > $('#aqTip').width() 
				? $obj.width():$('#aqTip').width();
			var x = p.left + ow + opts.marginX;
			if (x > document.body.clientWidth)
				x = p.left - ow - opts.marginX;

			$('#aqTip').show()
				.css({ left: x+'px', top: p.top+opts.marginY+'px' })
		} else
			$('#aqTip:visible').hide()

		return false;
	});
};

$.fn.aqTip.defaults = { 
	marginX: 10, marginY: 10, 
	css: { 
		backgroundColor: '#fff', color: '#444',
		border: '1px solid #ddd', padding: '5px' }
};
})(jQuery);
