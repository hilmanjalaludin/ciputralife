<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require("../class/class.application.php");
require('../sisipan/parameters.php');

function getDataCollMon()
	{
		global $db;
			$sql = " select 
							((a.SubCategoryId)-1) as s_form, 
							concat('A_INIT',a.SubCategoryId,'h') as s_object,
							concat('sliderValue',a.SubCategoryId,'h') as s_name,
							a.SubCategoryDesc as n_label, 
							a.StartNumber as n_minValue, 
							a.EndNumber as n_maxValue,
							'0' as n_value,
							a.StepNumber as n_step
							from coll_subcategory_collmon a
							where a.SubCategoryFlags=1 ";
			$qry = $db -> query($sql);		 
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[][$rows['s_object']] = array
				(
					's_form'  => $rows['s_form'],
					's_name'  => $rows['s_name'],
					'n_minValue' => $rows[n_minValue],
					'n_maxValue' => $rows[n_maxValue],
					'n_value' => $rows[n_value],
					'n_step' => $rows[n_step],
					'n_label' => $rows['n_label']
				); 
			}
			return $datas;
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title><?php echo $Themes->V_WEB_TITLE; ?> :: Coll Monitoring </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?rev=ext.v.0.2"></script>
	<script language="javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>	
	<link rel="shortcut icon" href="<?php echo $app->basePath();?>gambar/enigma.ico" />
	<link rel="stylesheet" type="text/css" href="styles.css" />
	<style>
		.text{width:25px;height:18px;font-size:11px;border:1px solid #ddd;background-color:#FFFCCC;} 
		.text-label{font-family:Arial;font-size:11px;}
	</style>
</head>
<body>
 <fieldset style="border:1px solid #dddddd;font-family:Arial;font-size:14px;">
	<legend style="color:blue;font-weight:bold;"> Call Scoring </legend>
	<script language="Javascript" src="<?php echo $app->basePath();?>js/slider.plugin.js"></script>
	<script language="JavaScript">
	
		var json_encode_datas =<?php echo json_encode(getDataCollMon()) ;?>;
		
	/* config TPL layout data  ********************/	
		var A_TPL4h = {
			'b_vertical' : false,
			'b_watch': true,
			'n_controlWidth': 100,
			'n_controlHeight': 17,
			'n_sliderWidth': 17,
			'n_sliderHeight': 16,
			'n_pathLeft' : 0,
			'n_pathTop' : 0,
			'n_pathLength' : 82,
			's_imgControl': '../gambar/side_col_bg.png',
			's_imgSlider': '../gambar/handle.gif',
			'n_zIndex': 1
		}
		
	/* render content data to document write ********************/
	
		for(top_object in json_encode_datas )
		{
			for(object_name in json_encode_datas[top_object] )
			{
				var class_var_object = json_encode_datas[top_object][object_name];
				var data_object_name= 
					{
						's_form' : parseInt(class_var_object['s_form']),
						's_name': class_var_object['s_name'],
						'n_minValue' : parseInt(class_var_object['n_minValue']),
						'n_maxValue' : parseInt(class_var_object['n_maxValue']),
						'n_value' : parseInt(class_var_object['n_value']),
						'n_step' :  parseInt(class_var_object['n_step']),
						'n_label' : class_var_object['n_label']
					}
					
				new slider(data_object_name,A_TPL4h);
			}
		}
	</script>
	</fieldset>
</body>
</html>