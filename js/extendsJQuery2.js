/*
	* extends Jquery pagging
	* author : omens
    * create date 2012-10-17	
*/

var extendsJQuery = {
	totalRecord:0,
	totalPage:0,
	showrecord:true,
	
	__custnav:'',
	__custlist:'',
	__listFields:{},
	__totalsPage:'',
	
	__mainContact:'dta_contact_detail.php',
	__mainContacts:'dta_submit_detail.php',
	__mainwaContact:'dta_wa_contact_detail.php',
	__showContact:'dta_show_contact_detail.php',
	__showContacts:'dta_show_contact_detail.php',
	
	construct:function(navigation, fields){
		this.__custnav 	 = (navigation.custnav!=''?navigation.custnav:'')
		this.__custlist	 = (navigation.custlist!=''?navigation.custlist:'')
		this.__listFields  = (fields!=''?fields:'')
	},
	
	postText:function()
	{
		var strText ='';
		for( i in this.__listFields){
			strText = strText+'&'+i+'='+this.__listFields[i]
		}
		strText = strText.substring(1,strText.length)
		return strText;
	},
	
	postContentList:function(){
		
		if( extendsJQuery.totalPage!=0){
			jQuery(function(){
				jQuery('#pager').aqPaging({
					current: 1, 
					pages: extendsJQuery.totalPage, 
					records:(extendsJQuery.totalRecord==''?0:extendsJQuery.totalRecord),
					rec:extendsJQuery.showrecord,
					flip: true, 
					cb: function(p){
						jQuery('.content_table').load((extendsJQuery.__custlist?extendsJQuery.__custlist:'')+'?'+(extendsJQuery.postText()?extendsJQuery.postText().replace(/\s+/g, '%20'):'')+'&v_page='+p);
					} 
				});
			})	
		}else{
			jQuery(function(){
				jQuery('#pager').aqPaging({
					current: 1, 
					pages:1, 
					records:extendsJQuery.totalRecord,
					flip: true, 
					cb: function(p){
						jQuery('.content_table').load((extendsJQuery.__custlist?extendsJQuery.__custlist:'')+'?'+(extendsJQuery.postText()?extendsJQuery.postText().replace(/\s+/g, '%20'):''));
					} 
				});
			})	
		}
		
	},
	
	postContent:function(){
		jQuery('#main_content').load((this.__custnav?this.__custnav:'notfound.php')+'?'+(this.postText()?this.postText().replace(/\s+/g, '%20'):''));
	},
	
/* set order data ***/
	
	orderBy:function(init_columns){
		order_init_type = (this.__listFields.type!=''?(this.__listFields.type=='ASC'?'DESC':'ASC'):'ASC');
		init_columns= (init_columns!==undefined?init_columns:'');
		//alert(init_columns+' '+order_init_type);
		if(init_columns)
		{
			extendsJQuery.__listFields.order_by = init_columns;
			extendsJQuery.__listFields.type = order_init_type;
		}
		
		extendsJQuery.postContent();
	},
	
	contactDetail:function(customerid,campaignid,CallReasonId){
		if( customerid.length >0  && campaignid.length >0 ){
			jQuery('#main_content').load(this.__mainContact+'?CustomerId='+customerid+'&CampaignId='+campaignid+'&CallReasonId='+CallReasonId);
			
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},
	
	contactDetails:function(customerid,campaignid,CallReasonId){
		if( customerid.length >0  && campaignid.length >0 ){
			jQuery('#main_content').load(this.__mainContacts+'?FuId='+customerid+'&CampaignId='+campaignid+'&CallReasonId='+CallReasonId);
			
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},
	
	showDetail:function(customerid,campaignid){
		if( customerid.length >0  && campaignid.length >0 ){
			jQuery('#main_content').load(this.__showContact+'?CustomerId='+customerid+'&CampaignId='+campaignid);
			
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},

	showDetails:function(customerid,campaignid){
		if( customerid.length >0  && campaignid.length >0 ){
			jQuery('#main_content').load(this.__showContact+'?CustomerId='+customerid+'&CampaignId='+campaignid);
			
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},
	
	verifiedContent:function(CustomerId, CampaignId, VerifiedStatus)
	{
		if( (CustomerId!='') && (CampaignId!='') ){
			jQuery('#main_content').load(this.__mainContact+'?CustomerId='+CustomerId+'&CampaignId='+CampaignId+'&VerifiedStatus='+VerifiedStatus);
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},
	
	verifiedWaContent:function(CustomerId, CampaignId, VerifiedStatus)
	{
		if( (CustomerId!='') && (CampaignId!='') ){
			jQuery('#main_content').load(this.__mainwaContact+'?CustomerId='+CustomerId+'&CampaignId='+CampaignId+'&VerifiedStatus='+VerifiedStatus);
			return;
		}
		else{
			alert('No Customer ID. Please try again..!');
			return false;
		}
	},
	
	Content:function()
	{
		if( doJava.File !='' )
		{
			var direct_file_datas = doJava.File+'?'+doJava.ArrVal();
			if( direct_file_datas!='' )
			{
				jQuery('#main_content').load(direct_file_datas);
			}
		}
	}
	
}