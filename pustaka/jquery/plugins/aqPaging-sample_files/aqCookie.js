(function($){
$.aqCookie = {
	domain: '.aquaron.com',
	secToExpire: 3153600000,

	get: function(carr) {
		if (typeof carr == 'string')
			carr = [carr];

		var hash = [];
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);

			for(var j=0;j<carr.length;j++) {
				var n = carr[j]+'=';
				if (c.indexOf(n) == 0) 
					hash[carr[j]] = c.substring(n.length,c.length);
			}
		}
		return hash;
	},

	set: function(k,v) {
		if (v) {
			var exp = new Date();
			exp.setTime(exp.getTime() + $.aqCookie.secToExpire);
			document.cookie = k + "=" + v + "; path=/; domain="+$.aqCookie.domain+"; expires="+ exp.toGMTString() + '";';
		} else
			document.cookie = k + "=; path=/; domain="+$.aqCookie.domain+"; expires=Thu, 01-Jan-1970 00:00:01 GMT;";
	},

	all: function(filter) {
		var hash = [];
		var ca = document.cookie.split(';');
		var re = new RegExp(filter);
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (!c.match(re))
				continue;
			hash.push(c.substring(0,c.indexOf('=')));
		}
		return hash;
	},

	del: function(k) { $.aqCookie.set(k) }
};
})(jQuery);
