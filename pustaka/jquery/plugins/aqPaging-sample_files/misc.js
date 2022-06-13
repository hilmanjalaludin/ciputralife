(function($){
$.fn.aqFormatPlugin = function(tag,options) {
	var opts = $.extend({ Bcolor: '#123', Icolor: '#999', Ucolor: '#369' }, options);
	return this.each(function() {
		var re = new RegExp('\\.('+tag+'.*?)\\b','g');
		$(this).html($(this).html()
			.replace(re,".<b>$1<\/b>")
			.replace(/(["'])[^'"]+\1/g,"<u>$&<\/u>")
			.replace(/\/\/.+$/gm,"<i>$&<\/i>")
		);
		$('b', this).css('color',opts.Bcolor);
		$('i', this).css('color',opts.Icolor);
		$('u', this).css({color:opts.Ucolor,textDecoration:'none'});
	});
};

$.fn.aqDescription = function(plugin,options) {
	var opts = $.extend({ }, $.fn.aqDescription.defaults, options);
	
	return this.each(function() {
		var $obj = $(this);
		$.post(opts.cgi, { func: 'get', name: plugin }, function(j){
			var o = eval(j);
			if (o.status == 'err') return false;

			if (o.desc)
				$obj.html(o.desc);

			if (o.edit)
				$obj.hover(
					function(){ $(this).css({backgroundColor: opts.bgColor}) },
					function(){ $(this).css({backgroundColor: 'transparent'}) }
				).one('click',function(){
					$(this).html(
					'<form name="f" onsubmit="return $.fn.aqDescription.edit('+o.id+')">'+
					'<textarea style="height:'+opts.height+'px;width:'+opts.width+'px" name="desc">'+o.desc+'<\/textarea>'+
					'<input class="btn" type="submit" value="Save">'+
					'<input type="hidden" name="name" value="'+o.name+'">'+
					'<\/form>');
				});
		});

		return false;
	});
};

$.fn.aqDescription.edit = function(id) {
	var opts = $.extend({ }, $.fn.aqDescription.defaults);
	var f = document.forms['f'];
	$.post(opts.cgi, {func: 'edit', name: f.name.value, desc: f.desc.value, id: id}, function(j) {
		var o = eval(j);
		if (o.status == 'err')
			alert('ERROR: Cannot edit plugin!');
		else
			document.location.reload();
	});
	return false;
};

$.fn.aqDescription.defaults = {
	cgi: '/-jquery/z/plugin', bgColor: '#fff',
	width: 750, height: 50
};
})(jQuery);

