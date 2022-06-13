/*  
 * jQuery plugin : toolbars jquery 
 * author : omens
 * cretaedate: 2012-10-14 
 * return function 
 */

(function($){
	$.fn.extToolbars = function(options){
		var opts = $.extend({ },$.fn.extToolbars.defaults,options);
		return this.each(function(){
			if (!$('.extToolbars',this).length) {
			
				$.fn.extToolbars.defaults.uniqID++;
				$('<div class="extToolbars" id="extToolbars'
					+$.fn.extToolbars.defaults.uniqID+'">') 
					.appendTo(this);

				$.fn.extToolbars.defaults.cbs[$.fn.extToolbars.defaults.uniqID]
					= opts.cb;

				$('.extToolbars',this).css(opts.css);
			}
			
			var $extToolbars = $('.extToolbars',this);
			var $extPid = $extToolbars.attr('id'); 
			var html = '';
			var $extIcon  =  opts.extIcon.length;
			var $extMenu  =  opts.extMenu.length;
			var $input	  =  (opts.extInput!=''?opts.extInput:0);
	
			for( var i=0; i < $extIcon; i++){
				html+= "<li style='display: inline;list-style-type: none;border-right:1px solid #eeeeee;padding-left:8px;padding-right:8px;'>"+
					   ""+($input?$.fn.extToolbars.input(opts.extOption,i):'')+
					   "<a href='javascript:void(0);' style='text-decoration:none;' id='"+opts.extMenu[i]+"' "+(opts.extMenu[i]==0?"":"onclick='"+opts.extMenu[i]+"();'")+ "  title='"+opts.extTitle[i]+"' style='margin-left:10px;'>"+
					   ""+(opts.extIcon[i]!=''?"<img src='"+(opts.extUrl?opts.extUrl:"")+"/"+opts.extIcon[i]+"' border='0'  align='middle' style='margin-top:-5px;' alt='0'>":'') +""+
					   ""+(opts.extText?"<span style='margin-left:8px;vertical-align:middle;border:0px;'>"+opts.extTitle[i]+"</span>":"")+"</a></li>";
			}
			$extToolbars.html(html);
		});
	}
	
	$.fn.extToolbars.input = function($datas,pos){
		var $int = $datas.length;		
		var html = '';
		for( var i = 0; i<$int; i++ ){
			if($datas[i].render==pos){
				switch($datas[i].type){
					case 'text':
						html+="<input type='text' name='"+$datas[i].name+"' id='"+$datas[i].id+"' "+($datas[i].width?"style='width:"+$datas[i].width+"px;'":"") +" value='"+$datas[i].value+"'>"; 
					break;
					
					case 'label':
						html+="<label name='"+$datas[i].name+"' id='"+$datas[i].id+"' "+($datas[i].width?"style='border:0px solid #000000;color:red;width:"+$datas[i].width+"px;'":"") +">"+$datas[i].label+"</label>"; 
					break;
					
					case 'combo':
						var $store = $datas[i].store
							html+= ($datas[i].header?'<b>'+$datas[i].header+'&nbsp;:&nbsp; </b>':'');
							html+="<select "+($datas[i].width?"style='width:"+$datas[i].width+"px;'":"") +" "+ ($datas[i].triger!=''?"onchange='"+$datas[i].triger+"(this.value);'":'') +" name='"+$datas[i].name+"' id='"+$datas[i].id+"'>";
							html+="<option value=''>--Choose--</option>";
								for(var x in $store ){
									for (var y in $store[x]){
										if( y==$datas[i].value)
										{
											html+="<option value='"+y+"' selected>"+$store[x][y]+"</option>"
										}
										else
										{
											html+="<option value='"+y+"'>"+$store[x][y]+"</option>"
										}		
									}
								}
							html+="</select>"; 
					break;	
					
					case 'multiple':
						var $store = $datas[i].store
							html+= ($datas[i].header?'<b>'+$datas[i].header+'&nbsp;:&nbsp; </b>':'');
							
							/*html+="<select "+($datas[i].width?"style='width:"+$datas[i].width+"px;'":"") +" "+ ($datas[i].triger!=''?"onchange='"+$datas[i].triger+"(this.value);'":'') +" name='"+$datas[i].name+"' id='"+$datas[i].id+"' >"; //
							html+="<option value=''></option>";
								for(var x in $store ){
									for (var y in $store[x]){
										if( y==$datas[i].value)
										{
											html+="<option value='"+y+"' selected>"+$store[x][y]+"</option>"
										}
										else
										{
											html+="<option value='"+y+"'>"+$store[x][y]+"</option>"
										}		
									}
								}
							html+="</select>"; **/
							html='<div class="box-shadow" style="z-index:9999;position:fixed;background-color:#dddd;border:1px solid #000000;"> <ul>';
								for(var x in $store ){
									for (var y in $store[x]){
										html+='<li>'+$store[x][y]+'</li>'		
									}
								}
							html+='</ul></div>';	
					break;

					
					
					
				}
			}	
		}
		//alert(html)
		return html;
		
		
	}
	
	$.fn.extToolbars.defaults = {
		cbs: [], pages: 0, current: 0, max: 10, uniqID: 0, flip: false,
		css: { fontFamily: 'arial', padding: '6px',border:0},
		blockCss: { display:'block', float:'left'},
		borderColor: '#444'
	};

})(jQuery);