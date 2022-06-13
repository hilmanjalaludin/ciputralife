$(document).ready(function(){
	// add background to content
	//$("#content").addLayer("url('images/left_login_bg.png') no-repeat bottom left");
	$("#content").addLayer("url('images/right_login_bg.png') no-repeat top right");

	// set first content
	content("recording_voice_logger");	

	// logo handler
	$("#logo").hide().slideDown(2000);

	// navigation handler
	$("#navigation").hide().show("slide", {percent: 50}, 2000);
	$("#navigation ul li:last-child").addClass("last");
	
	/**
	 * Left menu
	 */
	$("#accordion").accordion({
		autoHeight: false,
		icons: {
    			header: "ui-icon-circle-arrow-e",
   				headerSelected: "ui-icon-circle-arrow-s"
			}
	});
	$(".menu ul li:last-child").addClass("last");
	$(".menu ul li a").focus(function(){
		blur();
	});
	
}