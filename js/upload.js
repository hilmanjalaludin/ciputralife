 /*******************************************
 * filtype  : java script
 * filename : upload.js
 * author   : Rahmattullah( Omens)
 * Title    : demo upload by javascript	
 *****************************************/

var AjaxUploads = {


 /* config this class, with default parameter **/
	
	UploadsReady:function(e){},
	UploadStatus:false,
	
	UploadsConfig:{
		actToUploads   : 'upload.php',
		methodUploads  : 'POST',
		fileToUploads  : 'fileToupload',
		numberProgress : 'progressNumber',
		innerProgress  : 'prog',
		
		fileInfoUploads: {
				fileName :'fileName',
				fileType :'fileType',
				fileSize :'fileSize',
		}
	},
	
/* get value by id object ***/
	
	UploadsGetID :function(e){
		if(e){
			var __id = document.getElementById(e);
			return __id;
		}
	},
	
  /* information file to upload **/
	
	UploadInfo : function(){
		var uploadFile = AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileToUploads).files[0];
			if (uploadFile) {
                var uploadFileSize = 0;
                if (uploadFile){
                     uploadFileSize = (Math.round(uploadFile.size * 100 / 1024) / 100).toString() + 'KB';
				}
				
				AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileInfoUploads.fileName).innerHTML = 'Name: ' + uploadFile.name;
                AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileInfoUploads.fileType).innerHTML = 'Size: ' + uploadFileSize;
                AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileInfoUploads.fileSize).innerHTML = 'Type: ' + uploadFile.type;
               
            }
	},
	
/* action uploads file **/

	UploadsFile : function ()
	{
        var formUploads = new FormData();
            formUploads.append(AjaxUploads.UploadsConfig.fileToUploads, AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileToUploads).files[0]);
            var doUploads = new XMLHttpRequest(); // ajax function 
				doUploads.upload.addEventListener("progress", AjaxUploads.UploadsProgress, true);
				doUploads.addEventListener("load", AjaxUploads.UploadsComplete, true);
				doUploads.open(AjaxUploads.UploadsConfig.methodUploads, AjaxUploads.UploadsConfig.actToUploads);
				doUploads.send(formUploads);
			
    },	
	
		
	UploadsOther : function ()
	{
        var formUploads = new FormData();
            formUploads.append(AjaxUploads.UploadsConfig.fileToUploads, AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.fileToUploads).files[0]);
            var doUploads = new XMLHttpRequest(); // ajax function 
				doUploads.upload.addEventListener("progress", AjaxUploads.UploadsProgress, true);
				doUploads.addEventListener("load", AjaxUploads.UploadsOtherComplete, true);
				doUploads.open(AjaxUploads.UploadsConfig.methodUploads, AjaxUploads.UploadsConfig.actToUploads);
				doUploads.send(formUploads);
			
    },
	
/* on progress upload **/	

	UploadsProgress : function(evt) { // evt is object :
	
	
	/*
		********************************************************
		*	untuk melihat isi dari object evt
		*   gunakan :
		*	
		*	for( i in evt){
		*		alert('Object :'+i+',  content :'+evt[i]);
		*	}
		*
		**********************************************************
	*/
		
		if (evt.lengthComputable) {
				var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.numberProgress).innerHTML = percentComplete.toString() + '%';
                AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.innerProgress).value = percentComplete;
					if(percentComplete>=100){
						setTimeout(function(){
							AjaxUploads.UploadsGetID('progressNumber').style.display="none";
							AjaxUploads.UploadsGetID('prog').style.display="none";
							},2000
						);
					}
				
        }
        else {
               AjaxUploads.UploadsGetID(AjaxUploads.UploadsConfig.numberProgress).innerHTML = 'unable to compute';
            }
    },

	
/* if completed upload **/
	
	UploadsComplete:function(evt)
	{
		if( evt.target.DONE ) { this.UploadStatus = true; }
		if( this.UploadStatus )
		{
			try
			{
				AjaxUploads.UploadsGetID('loadings_gambar').style.display="none";
				 var message_error = JSON.parse(evt.target.responseText);
				 
				 if( message_error.result==1 )
				 {
					if( message_error.tot_other_campaign_rows > 0 )
					{
						if( confirm('You have ( '+message_error.tot_other_campaign_rows+' ) duplicate Customer on Other Campaign , Do you want Upload ? ')){
							AjaxUploads.ConfirmActionUpload('upload_replace_yes',message_error);
						}
						else{
							AjaxUploads.ConfirmActionUpload('upload_replace_no',message_error);
						}
					}
					
					var Message = " Info ,Total Rows ( "+message_error.tot_rows+" ),\n "+
								  " Success Upload ( "+message_error.tot_success_rows+" ) Rows, \n "+
								  " Failed Upload ( "+message_error.tot_failed_rows+" ) Rows, \n "+
								  " Total duplicate on the same Campaign ( "+message_error.tot_same_campaign_rows+" ) Rows, \n "+
								  " Total duplicate on other Campaign ( "+message_error.tot_other_campaign_rows+" )."; 
								  
					alert(Message);
					extendsJQuery.postContent();	
				}
				else{
					alert("Upload failed!\n Please check your template.");
				}
			}
			catch(err)
			{
				alert("");
			}
		}
	},
	
	ConfirmActionUpload : function(action,message_error)
	{
		doJava.File = '../class/class.replace.upload.php';
		doJava.Params = {
			action : action,
			CampaignUploadId : message_error.campaign_upload_id
		}
		var msg_error = doJava.eJson();
		if( msg_error.result==1)
		{
			alert(msg_error.msg);	
		}
	},
	
	
/* if completed upload **/
	
	UploadsOtherComplete:function(evt){
		if( evt.target.DONE ) {
			this.UploadStatus = true;
		}
		
		if( this.UploadStatus ){
			 AjaxUploads.UploadsGetID('loadings_gambar').style.display="none";
			 alert(evt.target.responseText)
		}
	}
	
}
