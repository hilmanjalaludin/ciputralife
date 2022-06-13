<!DOCTYPE html>
<html lang="en">

<head>
<title>Daily SQC Report</title>
<style>
	table.grid{}
	td.header { background-color:#2182bf;font-family:Arial;font-weight:bold;color:#f1f5f8;font-size:12px;padding:5px;} 
	td.sub { background-color:#eeeeee;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;} 
	td.subtot { background-color:#ef9b9b;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;} 
	td.content { padding:2px;height:24px;font-family:Arial;font-weight:normal;color:#456376;font-size:12px;background-color:#f9fbfd;} 
	td.first {border-left:1px solid #dddddd;border-top:1px solid #dddddd;border-bottom:0px solid #dddddd;}
	td.middle {border-left:1px solid #dddddd;border-bottom:0px solid #dddddd;border-top:1px solid #dddddd;}
	td.lasted {border-left:1px solid #dddddd; border-bottom:0px solid #dddddd; border-right:1px solid #dddddd; border-top:1px solid #dddddd;}
	td.agent{font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd; 
			border-bottom:0px solid #dddddd; border-right:0px solid #dddddd; border-top:0px solid #dddddd;
			background-color:#fcfeff;padding-left:2px;color:#06456d;font-weight:bold;}
	h1.agent{font-style:inherit; font-family:Trebuchet MS;color:blue;font-size:14px;color:#2182bf;}
	
	td.total{
				padding:2px;font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd; 
			border-bottom:1px solid #dddddd; border-top:1px solid #dddddd;  
			border-right:1px solid #dddddd; border-top:1px solid #dddddd;
			background-color:#2182bf;padding-left:2px;color:#f1f5f8;font-weight:bold;}
	span.top{color:#306407;font-family:Trebuchet MS;font-size:28px;line-height:40px;}
	span.middle{color:#306407;font-family:Trebuchet MS;font-size:14px;line-height:18px;}
	span.bottom{color:#306407;font-family:Trebuchet MS;font-size:12px;line-height:18px;}
	td.subtotal{ font-family:Arial;font-weight:bold;color:#3c8a08;height:30px;background-color:#FFFCCC;}
	td.tanggal{ font-weight:bold;color:#FF4321;height:22px;background-color:#FFFFFF;height:30px;}
	h3{color:#306407;font-family:Trebuchet MS;font-size:14px;}
	h4{color:#FF4321;font-family:Trebuchet MS;font-size:14px;}
</style>
</head>
<body>
<div class="label_header" style="margin-bottom:5px;padding-top:5px;padding-bottom:5px;border-bottom:1px solid #eee;width:'100%';">
<span class='top'>Report <?php echo $form['header']['name'];?></span><br/>
<span class='middle'>Call Monitoring Date : <?php echo (isset($filter['colmon_date'])?
													$filter['colmon_date']['start']." s/d ".$filter['colmon_date']['end']:"-" )
											?>
</span> | 
<span class='middle'>Selling Date : <?php echo (isset($filter['selling_date'])?
													$filter['selling_date']['start']." s/d ".$filter['selling_date']['end']:"-" )
											?>
</span><br/>
<span class='bottom'>Report Date : <?php echo date('d/m/Y H:i:s') ?></span>
</div>
<?php
	//variable misterius
	$remarks_form_id= 12;
	/**
	** 12 di dapat dari tabel coll_group_collmon
	**/
	
	$header_static=array(
		'PolicyNumber'=>"POLICY",
		'EFFDate'=>"EFF. DATE",
		'rec_duration'=>"DURASI",
		'InsuredFirstName'=>"NAME",
		'InsuredDOB'=>"DOB",
		'Premi'=>"PREMIUM",
		'ProductName'=>"PRODUCT",
		'CampaignName'=>"CAMPAIGN",
		'prospect'=>"PROSPECT",
		'PayerEmail'=>"EMAIL",
		'waktu_analisis'=>"WAKTU ANALIS",
		'status_report'=>"STATUS",
		'TM'=>"AGENT",
		'eff_date_comp'=>"EFF. DATE COMPLETE",
		'spv'=>"SPV",
		'mgr'=>"AM",
		'qc'=>"QC",
		'PhoneNum'=>"NO. TELP."
	);
	$score_static_header =array(
		'static_note'=>"NOTE",
		'static_status_system'=>"Status ALL System & Rec. Result",
		'static_plan'=>"Plan (Polis/TT)"
	);
	
	$no=0;
	echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				<td rowspan=\"2\" class=\"header first\" nowrap align=\"center\">NO</td>";
	foreach($header_static as $cols=>$cols_name)
	{
		echo "<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">".$cols_name."</td>";
	}
	
	foreach($header_collmon as $group_id=>$group_name)
	{
		$colspan=0;
		$rowspan=2;
		if(isset($form['sub_category'][$group_id]))
		{
			$colspan = count($form['sub_category'][$group_id]);
			$rowspan=1;
		}
		
		echo "<td rowspan=\"".$rowspan."\" colspan=\"".$colspan."\" class=\"header middle\" nowrap align=\"center\">".$group_name."</td>";
		if(isset($form['add_remark_category'][$group_id]) and $form['add_remark_category'][$group_id]==1)
		{
			echo "<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\"> Remaks ".$group_name."</td>";
		}
		
		
	}
	echo "<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">NO. CC & EXP. CARD / NO. SAVING</td>";
	foreach($score_static_header as $index_static=>$name_static)
	{
		echo "<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">".$name_static."</td>";
	}
	echo "</tr>";
	echo "<tr>";
	foreach($header_collmon as $group_id=>$group_name)
	{
		if(isset($form['sub_category'][$group_id]))
		{
			foreach($form['sub_category'][$group_id] as $sub_group_id=>$sub_group_name)
			{
				echo "<td class=\"header middle\" nowrap align=\"center\">".$sub_group_name."</td>";
			}
		}
	}
	echo "</tr>";
	
	foreach($policy as $cus_id => $insuredgroup)
	{
		$no++;
		$rowspan= count($policy[$cus_id]);
		$samecustid= $no;
		// echo "<tr>";
		foreach($insuredgroup as $insuredid => $cols_result){
			echo "<tr>";
			if($samecustid==$no){
				echo "<td rowspan=\" ".$rowspan." \" class=\"content first\" nowrap>".$no.". </td>";
			}
			foreach($header_static as $cols=>$cols_name)
			{
				echo "<td class=\"content middle\" align=\"center\" nowrap>".$cols_result[$cols]."</td>";
			}
			if($samecustid==$no){	
				foreach($header_collmon as $group_id=>$group_name)
				{
					
					if(isset($form['sub_category'][$group_id]))
					{
						foreach($form['sub_category'][$group_id] as $sub_group_id=>$sub_group_name)
						{
							$answer = (isset($col_result[$cus_id][$group_id][$sub_group_id])?
								$col_result[$cus_id][$group_id][$sub_group_id] :
								"-"
							);
							echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$answer."</td>";
						}
					}
					if(isset($form['add_remark_category'][$group_id]) and $form['add_remark_category'][$group_id]==1)
					{
						$remaks=(isset($remarks_group[$cus_id][$group_id])?$remarks_group[$cus_id][$group_id]:"-");
						echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$remaks."</td>";
					}
					if(isset($score_result[$cus_id][$group_id]))
					{
						echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$score_result[$cus_id][$group_id]."</td>";
					}
					/**
					** tanem dikit broh
					** prosesnya ada yg kelewat
					** waktu udah abis jadi biar cepet ^_^ :*
					**/
					if($remarks_form_id===$group_id)
					{
						for($i=1;$i<2;$i++)
						{
							$remaks=(isset($remarks_form[$cus_id][$i])?$remarks_form[$cus_id][$i]:"-");
							echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$remaks."</td>";
						}
						
					}
					//akhir tanem dikit =.=
				}
				echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$cols_result['PayerCreditCardNum']."</td>";
				foreach($score_static_header as $index_static=>$name_static)
				{
					$input=(isset($static_input[$cus_id][$index_static])?$static_input[$cus_id][$index_static]:"-");
					
					echo "<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$input."</td>";

				}
			}
			echo "</tr>";
			$samecustid++;
		}
	}
	echo "</table>";
?>	
</body>
</html>