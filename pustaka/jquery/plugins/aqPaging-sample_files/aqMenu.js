(function($){
	$.fn.aqMenu = function(arry,options) {
		var opts = $.extend({ }, $.fn.aqMenu.defaults, options);

		return this.each(function() {
			if (!$('.aqMenu',this).length) {
				$.fn.aqMenu.defaults.currentID = opts.currentID;
				$('<div class="aqMenu"><\/div>').appendTo(this);

				var $menu = $('.aqMenu',this);
				for (var i=0;i<arry.length;i++)
					$menu.append('<a title="'+arry[i][0]
						+'" href="javascript:void(0)" onclick="'
						+arry[i][2]+'">'+arry[i][1]+'<\/a>');
				$menu.append('<br style="clear:both">');

				$menu.find('a').css({
					display: 'block', float: 'left', 
					padding: '2px 5px', marginRight: '5px', 
					color: opts.hiColor, backgroundColor: opts.loColor
				}).hover(
					function(){ $(this).css({ 
						backgroundColor: opts.hiColor, 
						color: opts.loColor }) },
					function(){ 
						if ($.fn.aqMenu.defaults.currentID != $(this).attr('title')) 
							$(this).css({ 
								backgroundColor: opts.loColor, 
								color: opts.hiColor }) }
				);
			} else if (typeof arry != 'object')
				$.fn.aqMenu.defaults.currentID = arry;

			$('.aqMenu a',this)
				.css({ backgroundColor: opts.loColor, color: opts.hiColor });

			$('.aqMenu a[title="'+$.fn.aqMenu.defaults.currentID+'"]',this)
				.css({ backgroundColor: opts.hiColor, color: opts.loColor });
    
			return false;
		});
	};

$.fn.aqMenu.defaults = {
	hiColor: '#7F4F17', loColor: '#fff', currentID: ''
};
})(jQuery);
