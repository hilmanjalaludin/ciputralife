/* infoBar 1.0
   By: Alex Barberis
   Created on 3.11.2009 */

function showInfoBar(caption){
	hideInfoBar();
	$(document.body).append("<div id=infoBar><table width=100%><tr nowrap id=infoContent><td width=30></td><td align=left>" + caption + "</td><td width=60 align=left><div id=infoClose>[close]</div></td></tr></table></div>");
	$("#infoCallback").click();
	$("#infoCallback").css("border-color","#ffffff");
	$("#infoCallback").css("border","7");
	$("#infoCallback").css("cursor","hand");
	$("#infoClose").click(hideInfoBar);
	$("#infoClose").css("border-color","#ffffff");
	$("#infoClose").css("border","7");
	$("#infoClose").css("cursor","hand");
	$("#infoBar").hide();
	$("#infoContent").css("font-weight","bold");
	$("#infoContent").css("font","15px Arial,Verdana,sans-serif;");
	$("#infoContent").css("color","white");
	$("#infoBar").css("background-color","#ff0000");
	$("#infoBar").css("position","absolute");
    $(window).resize(info_position);
    $(window).scroll(info_position);
	info_position();
	$("#infoBar").slideDown(400); //i like sliding in more than fade in this case. i think it catches the users attention more effectively
	//$("#infoBar").fadeIn(800); //if you like this more remark the previous line

}

function hideInfoBar(){
	$("#infoBar").fadeOut(400)
	$("#infoBar").remove();
}

function info_position() {
	var scrollPosition = $(window).scrollTop();
	$("#infoBar").css("top",scrollPosition +"px");
	$("#infoContent").css("height","40px");
	$("#infoBar").css("left","0px");
	$("#infoBar").css("width",$(window).width());
}
