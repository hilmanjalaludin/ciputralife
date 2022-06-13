<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

/* function report type **/
class rpt_convertion extends mysql {
	function report_type(){
		return 
			$report_type = array(
				// 'cmp_overview' 			=> 'Campaign Overview',
				'agency_performance' 	=> 'Agent Performance',
				'performance_by_hour'  	=> 'Performance By Hour',
				'cmp_info_object'  		=> 'Campaign Information & Objective',
				'lead_activity'  		=> 'Lead Activity',
				'cmp_disposition'  		=> 'Campaign Disposition',
				'cmp_review'  			=> 'Campaign Review',
				'ref_report'  			=> 'Referal Report',
				//'report_free_pa'		=> 'Free PA Report' //ditambahkan oleh Fajar 27-01-2014
			);
	}

	function getspv() {
		$datas = array();
		if($this -> getSession('handling_type')==USER_SUPERVISOR){
			$sql = "select a.UserId as spvid, concat(a.id,' - ',a.full_name) as spvname
					from tms_agent a
					where a.UserId = ".$this -> getSession('UserId')." and a.user_state = 1 order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			$i = 1;
			while ($row = $this -> fetchassoc($qry)){
				$result[$row['spvid']] = $row['spvname'];
				$i++;
			}
		}else if ($this -> getSession('handling_type') > 0){
		
			$sql = "select a.UserId as spvid, concat(a.id,' - ',a.full_name) as spvname
					from tms_agent a
					where a.spv_id = a.UserId and a.user_state = 1 order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			$i = 1;
			while ($row = $this -> fetchassoc($qry)){
				$result[$row['spvid']] = $row['spvname'];
				$i++;
			}
		}
		return $result;
	}
}

$aInit = new rpt_convertion();

?>

<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/javaclass.js?versi=1.0"></script>
<!-- s cript type="text/javascript"  src="<?#php echo $app -> basePath();?>js/Cigna_calltracking.js?time=<?php echo time();?>"></scrip t -->
<script type="text/javascript">	
	var V_INDEX_REPORT = {
		HTML  : '../report/convertion_report.html.php',
		EXCEL : '../report/convertion_report.html.php'
	}

	var Cigna_report = function(){
		this.setJsonData = "../class/class.convertion_report.php"; 
	}
	
	Cigna_report.prototype.getJsonPHP = function(){
		return this.setJsonData;
	}
	
	Cigna_report.prototype.Cmp = function(ObjectName){
			return ( Cmp = {
				ElementId : ObjectName,
				SetIndex : function(index){
					doJava.dom(this.ElementId).options.selectedIndex = index; 
				},
				
				SetValue : function(values){
					doJava.dom(this.ElementId).value = values; 
				},
				
				getValue : function(){
					var chars = '';
					if( this.ElementId){ 
						if( this.getType() =='text'){ chars = doJava.dom(this.ElementId).value;}
						if( this.getType() =='checkbox'){ chars = doJava.checkedValue(this.ElementId)}
						if( this.getType() =='select-one'){ chars = doJava.SelArrVal(this.ElementId)}
					}
					return chars;
				},
				getType : function(){
					var chars = doJava.dom(this.ElementId);
					return chars.type
				}
			});	
	}

	Cigna_report.prototype.YUCKFOU = function(option){
			this.SegmentLabels(object = {
				labelText:'Telesales ', 
				labelId:'label_segment3_cigna', 
				source:{
					id:'content_segment3',
					type:'box',
					params:'get_filter_tm',
					value:option.value
				}	
			});
	}
	
	Cigna_report.prototype.SegmentLabels = function( object ){
		var Config = {
			labelSegmentText : object.labelText,
			labelSegmentId : object.labelId,
			DataSource : {
				SourceId :object.source.id,
				SourceParams :object.source.params,
				SourceValues :object.source.value,
				SourceReport_type : object.source.report_type,
				SourceType : object.source.type
			}
		}
		
		new (function(){
			doJava.File = __CIGNA.getJsonPHP();
			doJava.Params = {
				action : Config.DataSource.SourceParams,
				values : Config.DataSource.SourceValues,
				R_type : Config.DataSource.SourceReport_type,
				Type  : Config.DataSource.SourceType
			}
			doJava.Load(Config.DataSource.SourceId);
		});
		
		doJava.dom(Config.labelSegmentId).innerHTML = Config.labelSegmentText;
	}
	
	Cigna_report.prototype.HTML = function(){
			var Ext = new Cigna_report();
			var Supervisor 	 = Ext.Cmp('group_filter_segment2').getValue();
			var Telesales 	 = Ext.Cmp('group_filter_tm').getValue();	
			var start_date   = Ext.Cmp('start_date_cigna').getValue();
			var end_date     = Ext.Cmp('end_date_cigna').getValue();
			
		// set new section 	
			new ( function(){
				doJava.File = V_INDEX_REPORT.HTML;
				doJava.Params = {
					content : 'HTML', Supervisor : Supervisor, 
					Telesales : Telesales, start_date : start_date, 
					end_date  : end_date
				}
				window.open(doJava.getWindowUrl());
			})
	}
	
	Cigna_report.prototype.EXCEL = function(){
			var Ext = new Cigna_report();
			var Supervisor 	 = Ext.Cmp('group_filter_segment2').getValue();
			var Telesales 	 = Ext.Cmp('group_filter_tm').getValue();	
			var start_date   = Ext.Cmp('start_date_cigna').getValue();
			var end_date     = Ext.Cmp('end_date_cigna').getValue();
			
		// set new section 	
			new ( function(){
				doJava.File = V_INDEX_REPORT.EXCEL;
				doJava.Params = {
					content : 'EXCEL', Supervisor : Supervisor, 
					Telesales : Telesales, start_date : start_date, 
					end_date  : end_date
				}
				window.open(doJava.getWindowUrl());
			})
	}
	
	Cigna_report.prototype.createBars = function()
	{
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[[''],['Show HTML'],['Show Excel']],
				extMenu  :[[''],['__CIGNA.HTML'],['__CIGNA.EXCEL']],
				extIcon  :[[''],['zoom.png'],['zoom.png']],
				extText  :true,
				extInput :false,
				extOption:[{
							render : 0,
							type   : 'combo',
							header : 'Report Type ',
							id     : 'report_type', 	
							name   : 'report_type',
							store  : [],
							triger : '',
							width  : 250
						}]
				});
				
				$('#start_date_cigna').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
				$('#end_date_cigna').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
			});
	}
	
	var __CIGNA;
	new (function(){
		__CIGNA = new Cigna_report(); 
	});
	
	__CIGNA.createBars();
</script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:90px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>

<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Convertion Report</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<!-- t r>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Report Type</td>
					<td><?#php $jpForm->jpCombo('report_type_cigna','xx004', report_type(),NULL,'onchange="__CIGNA.ReportType(this);"',0,0);?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Group Filter By</td>
					<td><?#php $jpForm->jpCombo('group_filter_by_cigna','xx004', array('campaign'=>'Campaign Name','supervisor'=>'Supervisor','Telesales'=>'Telesales'),NULL,'onchange="__CIGNA.GroupFilter(this);"',NULL,0,0);?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment1_cigna">Campaign Name</span></td>
					<td> <span id="content_segment1"> <?#php $jpForm->jpCombo('group_filter_segment1','xx002',array(),NULL);?></span> </td>
				</t r  -->
				
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment2_cigna">Supervisor</span></td>
					<td> <span id="content_segment2"> <?php $jpForm->jpCombo('group_filter_segment2','xx002',$aInit->getspv(),NULL,'onchange="__CIGNA.YUCKFOU(this);"');?></span> </td>
				</tr>
			
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment3_cigna">Telesales</span></td>
					<td> <span id="content_segment3"><?php $jpForm->jpCombo('group_filter_segment3','xx002',array());?></span></td>
				</tr>
				
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Interval</td>
					<td>
						<?php $jpForm->jpInput('start_date_cigna','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date_cigna','xx002');?>
					</td>
				</tr>
				
				<!-- t r>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"><span id="label_segment4_cigna">Mode</span></td>
					<td><span id="content_segment4"><?#php $jpForm->jpCombo('mode_cigna','xx004', array('summary'=>'Summary'),NULL,NULL,0,0);?></span></td>
				</t r -->
				
			</table>
		</div>
		<div id="toolbars"></div>
</fieldset>	