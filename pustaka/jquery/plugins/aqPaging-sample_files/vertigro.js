(function($){
	$.fn.vertigro = function($max,$div) {
		return this.filter('textarea').each(function() {
			var grow = function(e) {
				if ($max && $div) {
					if ($(this).val().length > $max && e.which != 8)
						return false;
					$('#'+$div).html($max-$(this).val().length);
				}
				if (this.clientHeight < this.scrollHeight)
					$(this).height(this.scrollHeight 
					+ (parseInt($(this).css('lineHeight').replace(/px$/,''))||20)
					+ 'px');
			};
			$(this).css('overflow','hidden').keydown(grow).keyup(grow).change(grow);
		});
	};
})(jQuery);
