(function($){
	$.testPlug = function (){
		alert("hello");
		
		// Get the container element from the page
		var html = document.getElementById( 'panel_user' );
		
		// If the container doesn't yet exist, we need to create it
		if ( !html )
		{
			html = '<div id="panel_user" style="z-index:999;position:absolute;width:200px;height:300px;float:center;">Hello World </div>';
		}
		
		// Convert cont to a jQuery object
		html = $( html );
		
		$('#notification_user').append( html );
		$('#notification_user').fadeIn(20000)
	}
		
	$.fn.testPlug = function(){
		this.each(function()
			{
				new $.testPlug(); 
			}
		);
		return this;
	}		
	 
 })(jQuery);