<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	require("../class/lib.form.php");
	
	
?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/sackAjax.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/autocompletes.js"></script>
<script type="text/javascript">
	$(function(){
			/*$('#userGroup').corner();
			$('#menu_available').corner();
			$('.corner').corner();*/
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Search'],['Clear'],['Play'],['Download']],
				extMenu  :[['searchCustomer'],['resetSeacrh'],['play'],['download']],
				extIcon  :[['zoom.png'], ['cancel.png'],['control_play_blue.png'],['disk.png']],
				extText  :true,
				extInput :false,
				extOption:[{
						render : 4,
						type   : 'combo',
						header : 'Call Reason ',
						id     : 'v_result_customers', 	
						name   : 'v_result_customers',
						triger : '',
						store  : Reason
					}]
			});
			
			$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
			$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		});
		
		var RenderAjax = function(handle,UserId)
		{
			doJava.File 	= '../class/class.mon.recording.new.php';
			doJava.Params 	= {
				action	: 'get_data_user',
				handle	: handle,
				UserId	: UserId
			}
			return doJava.eJson();
		}
		
		var searchCustomer = function(){
		
			var cust_number  = doJava.dom('cust_number').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			
			var campaign_id  = doJava.dom('campaign_id').value;
			var call_result  = doJava.dom('call_result').value;
			var user_id 	 = doJava.dom('user_id').value;	
			var destination  = doJava.dom('destination').value;	
			var start_date 	 = doJava.dom('start_date').value;	
			var end_date 	 = doJava.dom('end_date').value;	
				doJava.File = '../class/class.mon.recording.php' 
		
				datas = {
					action		: 'get_data_null',
					cust_number : cust_number,
					cust_name 	: cust_name,
					campaign_id : campaign_id, 
					call_result : call_result, 
					user_id 	: user_id,
					destination : destination,	
					start_date 	: start_date,	
					end_date 	: end_date
			
				}
				
			var JsonData = doJava.eJson();
			doJava.dom('QtyData').innerHTML = JsonData.table;
			//doJava.dom('loading_images').innerHTML = '<span style="color:green;">&nbsp;Record(s)&nbsp;: '+JsonData.total+'  </span>';
			//doJava.dom('alloc_data_size').value = 0;
		}
		/*//////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		var defaultPanel = function(){
			doJava.File = '../class/class.mon.recording.php' 
		
			if( doJava.destroy() ){
				doJava.Method = 'POST',
				doJava.Params = { 
					action		:'tpl_onready', cust_number : datas.cust_number,
					cust_name 	: datas.cust_name, cust_dob 	: datas.cust_dob, 
					home_phone  : datas.home_phone, office_phone: datas.office_phone,
					mobile_phone: datas.mobile_phone,  campaign_id : datas.campaign_id, 
					call_result : datas.call_result,  user_id 	: datas.user_id,
					start_date  : datas.start_date, end_date	: datas.end_date, destination	: datas.destination
				}
				doJava.Load('span_top_nav');	
			}
		} 
		
		doJava.onReady(
			evt=function(){ 
			  defaultPanel();
			},
		  evt()
		)
		
		var searchCustomer = function(){
		
			var cust_number  = doJava.dom('cust_number').value; 
			var cust_name 	 = doJava.dom('cust_name').value;
			
			var campaign_id  = doJava.dom('campaign_id').value;
			var call_result  = doJava.dom('call_result').value;
			var user_id 	 = doJava.dom('user_id').value;	
			var destination  = doJava.dom('destination').value;	
			var start_date 	 = doJava.dom('start_date').value;	
			var end_date 	 = doJava.dom('end_date').value;	
				doJava.File = '../class/class.mon.recording.php' 
		
				datas = {
					cust_number : cust_number,
					cust_name 	: cust_name,
					campaign_id : campaign_id, 
					call_result : call_result, 
					user_id 	: user_id,
					destination : destination,	
					start_date 	: start_date,	
					end_date 	: end_date
			
				}
				
			extendsJQuery.construct(navigation,datas)
			extendsJQuery.postContent()
		}
		*////////////////////////////////////////////////////////////////////////////
</script>

<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Move New Data </legend>			
			<div class="box-shadow">
				<table cellpadding="3px;"  width="70%" >
					<tr>
						<td class="text_header"> Customer ID</td>
						<td>
							<input type="text" name="cust_number" id="cust_number" 
								   value="<?php echo ($this->havepost('cust_number')?$this->escPost('cust_number'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						
						
						<td class="text_header"> Campaign</td>
						<td>
							<select name="campaign_id" id="campaign_id" class="select">
								<option value=""> -- Choose --</option>
								<?php $this -> getCampaignAssigment( $this->escPost('campaign_id') ); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="text_header"> Customer Name</td>
						<td>
							<input type="text" name="cust_name" id="cust_name" 
								   value="<?php echo ($this->havepost('cust_name')?$this->escPost('cust_name'):'');?>" 
								   class="input_text" style="width:180px;height:18px;margin-right:2px;">
						</td>
						
						
						<td class="text_header"> &nbsp;</td>
						<td>
							
						</td>
					</tr>
					<tr>
						<td class="text_header"> Destination </td>
						<td>
							<input type="text" name="destination" id="destination" 
								   value="<?php echo ($this->havepost('destination')?$this->escPost('destination'):'');?>"
								   class="input_text" style="width:180px;height:18px;">
						</td>
						
						<td class="text_header"> Call Result </td>
						<td>
							<select name="call_result" id="call_result" class="select">
								<option value=""> -- Choose --</option>
								<?php $this->getResultStatus( $this->escPost('call_result') ); ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<td class="text_header"> Interval </td>
						<td>
							<input type="text" name="start_date" id="start_date" 
								   value="<?php echo ($this->havepost('start_date')?$this->escPost('start_date'):'blank');?>" class="input_text" style="width:70px;height:18px;">
							&nbsp; -&nbsp;	   
							<input type="text" name="end_date" id="end_date" 
								   value="<?php echo ($this->havepost('end_date')?$this->escPost('end_date'):'blank');?>" class="input_text" style="width:70px;height:18px;">	   
						</td>
						
						<td class="text_header"> User ID </td>
						<td>
							<select name="user_id" id="user_id" class="select">
								<option value=""> -- Choose --</option>
								<?php $this->getUserList($this->escPost('user_id')); ?>
							</select>
						</td>
					</tr>
					
				</table>
			</div>
	<div id="toolbars" class="toolbars"></div>
	<div class="content_table" id="QtyData"></div>
</fieldset>