/*****************************************************************************************
 **	Class doJava before class jpForm then upgrade add new 							    **
 ** function winew = window.open prototype 												**
 ** for attribut form in html 															**
 **	Cretaed By omns[rahmattullah], 														**
 **	Created Date 2012-02-05																**
 **																						**
 **  - Update 																			**
 **		@ add Func   : doDays.genDate(), doDays.MYdate(),doDays.SetValue()				**
 **		@ add Object : winew.winconfig{},												**
 **		@ add Func   : winew.open(),													**
 **																						**
 **  - Last update  - 2012-05-02 02:05 AM												**
 **																						**
 **     @ add Object : apendHTML.innerById												**	
 **     @ add Func 	 : apendHTML.innerInput											    **
 **     @ add Object : apendHTML.innerById												**
 **																						**	
 **  - Last update 2012-05-03, author: omens											**
 **  - Last Update 2013-08-29, author: omens											**
 **		@ add encode base64 <encrypt>													**
 **		@ add decode base64 <decrypt>													**
 *****************************************************************************************/	

var doJava ={
	
	isMSIE  :(navigator.appName=="Microsoft Internet Explorer"),
	File    : '',
	Params  : '',
	Method  : '',
	disArr  : '',
	msgBox  : '',
	sortBy  : '',
	sortOpt : '',
	
	dom:function(inputObj){
		return document.getElementById(inputObj);
	},
	
/* on ready function **/
	
	onReady:function(e){},		
 

/* show alert in do java**/
	showMessage:function(strText){
		if(strText.toString().length >0 ){
			alert(strText);
		}
	},
 
/* get element document on selected id */
	Value:function(inputObj){
		if (inputObj!=''){
			return this.dom(inputObj).value;
		}
	},
	
	name:function(op){
		if(op!=''){
			return document.getElementsByName(op);
		}
	},
	
	init :new Array(),
	
	setValue : function(text){
		for(i in this.init){
			doJava.dom(this.init[i]).value = text;
		}
		return ;		
	},
/* msgBox result string if call Post() mthod**/
	MsgBox :function(){alert(this.ArrVal())},
   
/* get value attribut selected  */	
    SelValue:function(inputObj){
	 
		if(inputObj.length>0){
				sel    = this.dom(inputObj);
				selVal = sel.options[sel.selectedIndex].value;
			return selVal; 
		}
		else{
			sel	   = inputObj;
			selVal = sel.options[sel.selectedIndex].value;
		  return selVal;
		}
	},
	
	ArrVal:function(){
		o='';
		 for(i in this.Params){
			o=o+''+i+'='+this.Params[i]+'&';
		 }
		 o=o.substring(0,(o.length-1));
		return o.replace(/\s+/g, '%20'); 
	},
   
   
   /* CLEAR CACHE ON BROWSER JS */
   
   
   destroy:function(){ 
		o='';
		 for(i in this.Params){
			i+'=';
		 }
		return true;
   },
   
   getWindowUrl:function()
   {
		if(this.File!=''){
			return this.File+'?'+this.ArrVal();
		}
		else
			return null;
   },
   
   Explode:{
		delimiter :'|',
		htmlvalue :'',
		response  :function(){
			this.htmlvalue =  doJava.Post().split(this.delimiter); 
		}
    },
	
   /* get text attribut selected  */
	SelText:function(inputObj){
		if(inputObj.length>0){
				sel    = this.dom(inputObj);
				selTxt = sel.options[sel.selectedIndex].text;
			return selTxt; 
		}
		else{
			sel	   = inputObj;
			selTxt = sel.options[sel.selectedIndex].text;
		  return selTxt;
		}
	},

    /*edited*/
	onSelectText:function(opt){
		obj = opt.options[opt.selectedIndex];
		if(obj.value=='all'){
			for(i=1; i<opt.options.length; i++){
				opt.selectedIndex=0;
			}
		}
	},
	
	SelArrVal:function(inputObj){
		strObj = '';
		selObj = this.dom(inputObj);
		if(selObj==null){
			return '';
		}else{
		
		 for(var i=0; i<selObj.options.length; i++){
		   if (selObj.options[i].selected){ 
			   strObj =strObj+','+selObj.options[i].value;
		   }
		 }
		 
	   		valObj=strObj.substring(1,strObj.length);
	   		return valObj;
		}
	 },
	 
	 getSelectValue:function(inputObj)
	 {
		strObj = '';
		selObj = this.dom(inputObj);
		if(selObj==null){
			return '';
		}else{
		
		 for(var i=0; i<selObj.options.length; i++){
		   strObj =strObj+','+selObj.options[i].value;
		   
		 }
		 
	   		valObj=strObj.substring(1,strObj.length);
	   		return valObj;
		}
		
	 },
	 
 /* start : tambahan object Move text */
 
	addOption:function(theSel_1, theSel_2, theValue)
	{
		var newOpt = new Option(theSel_2, theValue);
		var selLength = theSel_1.length;
		theSel_1.options[selLength] = newOpt;
	},
	 
	moveOptions:function(theSelFrom, theSelTo)
	{
	
		var selLength = theSelFrom.length;
		var selectedText = new Array();
		var selectedValues = new Array();
		var selectedCount = 0;
		var i;
			  
			for(i=selLength-1; i>=0; i--){
			
				if(theSelFrom.options[i].selected){
				  selectedText[selectedCount] = theSelFrom.options[i].text;
				  selectedValues[selectedCount] = theSelFrom.options[i].value;
				  this.deleteOption(theSelFrom, i);
				  selectedCount++;
				}
			}
			  
			
			for(i=selectedCount-1; i>=0; i--){
				this.addOption(theSelTo, selectedText[i], selectedValues[i]);
			}
	},
	
	deleteOption:function(theSel, theIndex)
	{ 
	  var selLength = theSel.length;
		  if(selLength>0)
		  {
			theSel.options[theIndex] = null;
		  }
	},


  /* stop : tambahan object Move text */ 
	
	serialize:function(byserialize){
		var __full_string = '';
		if( byserialize)
		{
			var elem = document.getElementsByTagName(byserialize);
			   for( var i in elem)
			   {
					var __str =  ( elem[i]?elem[i].name:false);
					var __obj = this.dom(__str);
					
						if(__obj)
						{
							__full_string = __full_string+"&"+__str+"="+__obj.value;	
						}
			   }
			   
			   if( __full_string )
			   {
					return __full_string = __full_string.substring(1,__full_string.length);	
			   }
			   else 
					return false;
		}
	},
	
	/* Post data to file **/
	
	 Post:function()
	 {
		 postVar   = this.File+'?'+this.ArrVal();
			xmlGet = null;
			xmlGet = new XMLHttpRequest();
			if(this.Method.length>0){
				xmlGet.open(this.Method, postVar,false);
			}
			else{
				xmlGet.open("GET", postVar, false );
			}
			
			xmlGet.send( null );
		return xmlGet.responseText;
     },
	 
	eJson :function()
	  {
		 postVar   = this.File+'?'+this.ArrVal();
			xmlGet = null;
			xmlGet = new XMLHttpRequest();
			if(this.Method.length>0){
				xmlGet.open(this.Method, postVar,false);
			}
			else{
				xmlGet.open("GET", postVar, false );
			}
			
			xmlGet.send();
			var jsondata = JSON.parse(xmlGet.responseText);
			return jsondata;
     },
	 
	 windowOpen:function()
	 {
		var window_url_position = this.File+'?'+this.ArrVal();
		if( window_url_position )
		{
			return window.open( window_url_position );
		}
	 },
	 
	 Load:function(inputObj)
	 {
		if(inputObj.length>0)
		{
			textArea = this.dom(inputObj);
			textArea.innerHTML = this.Post();
		}			
	 },
	 
	 connector:function()
	 {
		alert('congrats connect to javaclass ...');
	 },
	 
	 winew: 
	 { 
			  winHwnd:false,
			  winconfig:{
					location:'javaclass.php',width:100,height:150,windowName:'windowName',
					resizable:false, menubar:false, scrollbars:false, status:false, toolbar:false,
					left :0, 
					top : 0
				},
			  open:function(){ 
					var windowFeatures; windowFeatures = '';
					if (doJava.winew.winconfig.width != '' && doJava.winew.winconfig.width!= null){
						windowFeatures = windowFeatures+'screenX='+doJava.winew.winconfig.width+',';
					}
					if (doJava.winew.winconfig.height != '' && doJava.winew.winconfig.height!= null){
						windowFeatures = windowFeatures+'screenY='+doJava.winew.winconfig.height+',';
					}
					if (doJava.winew.winconfig.resizable){
						windowFeatures = windowFeatures+'resizable,';
					}
					if (doJava.winew.winconfig.location){
						windowFeatures = windowFeatures+'location,';
					}
					if (doJava.winew.winconfig.menubar){
						windowFeatures = windowFeatures+'menubar,';
					}
					if (doJava.winew.winconfig.scrollbars){
						windowFeatures = windowFeatures+'scrollbars,';
					}
					if (doJava.winew.winconfig.status){
						windowFeatures = windowFeatures+'status,';
					}
					if (doJava.winew.winconfig.toolbar){
						windowFeatures = windowFeatures+'toolbar,';
					}
					if (doJava.winew.winconfig.left){
						windowFeatures = windowFeatures+'left='+doJava.winew.winconfig.left+',';
					}
					if (doJava.winew.winconfig.top){
						windowFeatures = windowFeatures+'top='+doJava.winew.winconfig.top+',';
					}
					
				this.winHwnd = window.open(doJava.winew.winconfig.location, doJava.winew.winconfig.windowName, windowFeatures);	
				
				},
				
				winClose:function(){
					this.winHwnd=false;
					window.close(doJava.winew.winconfig.windowName);
				},
				
				opener:function(){
					window.open(doJava.File+'?'+doJava.ArrVal()); return;
				}
	 },
	 
	 argDisabled:function(arg)
	 {
		if(arg==true)
		{
			for(x in this.disArr)
			{ 
				this.dom(this.disArr[x]).value='';
				this.dom(this.disArr[x]).disabled=true;
			}	
		}
		else if(arg==false)
		{
			for(x in this.disArr)
			{ 
				this.dom(this.disArr[x]).value='';
				this.dom(this.disArr[x]).disabled=false;
			}	
		}
	 },
	 
	 Readonly:function(arg)
	 {
		if(arg==true)
		{
			for(x in this.disArr)
			{ 
				this.dom(this.disArr[x]).readOnly=true;
			}	
		}
		else if(arg==false)
		{
			for(x in this.disArr)
			{ 
				this.dom(this.disArr[x]).readOnly=false;
			}	
		}
	 },
	 
	 allowNumber:function(txt,obj)
	 {
		if (txt.keyCode < 44 || txt.keyCode > 57 || txt.keyCode==45 || txt.keyCode==47)
		{
			txt.returnValue = false;
			tmp = this.dom(obj).value;
		}
		
		if(txt.returnValue==false)
		{
			this.dom(obj).value=tmp.substring(0,(tmp.length-1));
		}
	},
	 
	 checkedValue:function(inputObj)
	 {
		 chkString='';
		 dNumber= document.getElementsByName(inputObj);
		  for(x=0; x<dNumber.length; x++)
		  {
			if(dNumber[x].checked)
			{
			 chkString=chkString+','+dNumber[x].value;
			}
		  }
		 chkString=chkString.substring(1,(chkString.length));
		return chkString;
	 },
	 
	 checkedAll:function(inputObj)
	 {
		 dNumber= document.getElementsByName(inputObj);
		  for(x=0; x<dNumber.length; x++)
		  {
			if(dNumber[x].checked==false)
			{
				dNumber[x].checked=true;
			}
			else
			{
				dNumber[x].checked=false;
			}
		  }
	 },
	 
	 optDisabled:function(inputObj)
	 {
		 dNumber= document.getElementsByName(inputObj);
		  for(x=0; x<dNumber.length; x++)
		  {
			dNumber[x].disabled = true;
		  }
	 },
	 
	 
	 todaysDate:function()
	 {
		var now = new Date();		
		var dtp = (now.getMonth()+1).toString();
			if(dtp.length==1)
			{
				var stamp = now.getDate()+' - 0'+(now.getMonth()+1)+' - '+now.getFullYear();
				return stamp;
			}
			else
			{
				var stamp = now.getDate()+' - '+(now.getMonth()+1)+' - '+now.getFullYear();
				return stamp;
			}
	 },
	 
	 uncheckedAll:function(inputObj)
	 {
		 dNumber= document.getElementsByName(inputObj);
		  for(x=0; x<dNumber.length; x++)
		  {
			dNumber[x].checked=false;
		  }
	 },
	 
	doDays:
	{
		genDate:function()
		{
		   return new Date();
		},		
		
		MYdate:function(strOpt)
		{
		  if(strOpt!=''){
			return dateFormat(this.genDate(),strOpt);
		  }else{
			return dateFormat(this.genDate(),"yyyy-mm-dd");
		  }
		},		
		
		SetValue:function(obj)
		{
			doJava.dom(obj).value= this.MYdate('');
		}
	},
	 
	apendHTML :{
			innerById  :'mytab',
			innerInput :function(type,name){
				var root = doJava.dom(this.innerById).getElementsByTagName('tr')[0].parentNode;
				var oR   = this.cE('tr');
				var oC   = this.cE('td');
				var oI   = this.cE('input'); 
				var oS   = this.cE('span')
				
				this.cA(oI,'type',type);
				this.cA(oI,'name',name);
				this.cA(oI,'id',name);
					oI.style.border 	= '1px solid #e0f';
					oI.style.width  	= '400px';
					oI.style.fontSize	= '12px';
					oI.style.height 	= '16px';
					oI.style.margin 	= '0px 4px 0px 0px';
					oS.style.cursor		= 'pointer';
					
				oS.onclick = function(){
					this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)
				}
				
				oS.appendChild(document.createTextNode('Remove'));
				oS.style.color='red';
				oS.style.fontWeight='bold';
				oC.appendChild(oI);
				oC.appendChild(oS);
				oR.appendChild(oC);
				root.appendChild(oR);
			},
		
			cE:function(el){
				this.obj = document.createElement(el);
				return this.obj;
			},
		
			cA:function(obj,att,val){
				obj.setAttribute(att,val);
					return
			}
    },
	
	 
	OverText:function(from,to){
		var __f = (from?from:'');
		var __t = (to?to:'');
	
			if(__f)
			{
				this.dom(__t).readOnly = 1;
				this.dom(__t).value = this.dom(__f).value
			}
			else{
				this.dom(__t).readOnly = 0;
				this.dom(__t).value = '';
				}
	 },
	 
	 OverSelect:function(from,to)
	 {
		var __f = (from?from:'');
		var __t = (to?to:'');
				
			if( __f ){	
				var __foo =  this.dom(__f);
				var __too =  this.dom(__t);
					__too.disabled=1;
					__too.selectedIndex = __foo.selectedIndex
					
			}
			else
				{
					var __too =  this.dom(__t);
					__too.disabled = 0;
					__too.selectedIndex = 0; 
				}
					
	 },
	 
	 OverChecked:function(cond,to)
	 {
		if( cond ){
			this.dom(to).checked = cond;
			this.dom(to).disabled = cond;
		}
		else
			{
				this.dom(to).checked = cond;
				this.dom(to).disabled = cond;
			}	
	 },
	 
	 NumericOnly:function(obj)
	 {
			var Lstring;
			var Lconstant;
			var Rstring;
				Lstring = obj.value.length;
				Lconstant = (obj.value.length -1)
				Rstring = obj.value.substring(Lconstant,Lstring)
				
			if(isNaN(Rstring)){
				obj.value = obj.value.substring(0,obj.value.length-1);
			}
	},
	
	MaskingText:function(o)
	{
		var __left_string = o.value;
			if( parseInt(__left_string.substring(0,2)) >12 ) o.value='';		
				if( __left_string.length >=3 )
				{
					if( __left_string.indexOf('/')<0 )
					{
						o.value='';		
						o.value='';		
					}
				}
	},
	
	Base64:{
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
		encode : function (input) {
			var output = "";
			var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
			var i = 0;

			input = doJava.Base64._utf8_encode(input);

			while (i < input.length) {

				chr1 = input.charCodeAt(i++);
				chr2 = input.charCodeAt(i++);
				chr3 = input.charCodeAt(i++);

				enc1 = chr1 >> 2;
				enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
				enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
				enc4 = chr3 & 63;

				if (isNaN(chr2)) {
					enc3 = enc4 = 64;
				} else if (isNaN(chr3)) {
					enc4 = 64;
				}

				output = output +
				this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
				this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

			}

			return output;
		},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
		}
	},
	
/* set masking text ***/
	
	setMasking:function(text)
	{
		var StringText 	 = '';
		var filterLength = ((text.length)-3);
			StringText 	 = StringText+''+text.substring(0,filterLength);
		var filterValue  = StringText.length;
			for(var i=(filterValue+1); i<text.length; i++)
			{
				StringText = StringText+'x';
			}
		return StringText;
	}
}
