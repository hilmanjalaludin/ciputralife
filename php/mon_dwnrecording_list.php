<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	function constructDate(){
		$date  = explode("-",$_REQUEST['filter_close']);
		$year  = substr($date[2],2,2);
		$month =  $date[1];
		$days  =  $date[0];
		$string = $days.$month.$year;
		if( $string!='') return $string;
	}
	
	/** create object **/
	
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		$sql = " select RecId,  date_format(a.RecDate,'%d-%m-%Y') as RecDate, a.RecFileName, 
					if(a.RecStatusDownload<>0,'Re-download','Ready to download') as status,
					a.RecSummarySize,
					a.RecSumaryFile,a.RecDateDownload,
					b.full_name as RecUserDownload
					from t_gn_recording a
					left join tms_agent b on a.RecUserDownload=b.UserId ";
					
					
		$ListPages -> query($sql);
		$filter ='';
		$my_dates = constructDate();
		if($db->havepost('filter_close')) $filter = " and a.RecFileName REGEXP('".$my_dates."') ";
		
		$ListPages -> setWhere($filter);
		$ListPages -> OrderBy(" date(RecDate)","DESC");
		$ListPages -> setLimit();
		$ListPages -> result();
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first" width="5%">&nbsp;<a href="javascript:void(0);" >#</a></th>	
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>	
		<th nowrap class="custom-grid th-middle">&nbsp;Voice Date </th>        
        <th nowrap class="custom-grid th-middle" align="left">&nbsp;File Name</th>
		<th nowrap class="custom-grid th-middle" align="center">&nbsp;Totals File</th>
		<th nowrap class="custom-grid th-middle">&nbsp;File Size</th>		
		<th nowrap class="custom-grid th-middle">&nbsp;Download Status</th>
		<th nowrap class="custom-grid th-middle">&nbsp;User Download</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Download Date</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		$qry = $db -> query( $ListPages -> getSQL() );
		foreach( $qry -> result_assoc() as $rows )
		{
			$color = ($no%2!=0?'#FFFEEE':'#FFFFFF');
			?>
			<tr CLASS="onselect" bgcolor="<?php echo $color;?>">
				<td class="content-first"><input type="checkbox" value="<?php echo $rows['RecId']; ?>" name="chk_rec" id="chk_rec"></td>
				<td class="content-middle"><?php echo $no ?></td>
				<td class="content-middle" align="center"><?php echo ($rows['RecDate']?$rows['RecDate']:'-'); ?></td>
				<td class="content-middle" align="left"><?php echo ($rows['RecFileName']?$rows['RecFileName']:'-'); ?></td>
				<td class="content-middle" align="center"><?php echo ($rows['RecSumaryFile']?$rows['RecSumaryFile']:'0'); ?></td>
				<td class="content-middle" align="center"><?php echo ($rows['RecSummarySize']?formatSize($rows['RecSummarySize']):formatSize(0)); ?></td>
				<td class="content-middle" align="center"><?php echo ($rows['status']?$rows['status']:'-'); ?></td>
				<td class="content-middle" align="center"><?php echo ($rows['RecUserDownload']?$rows['RecUserDownload']:'-'); ?></td>
				<td class="content-lasted" align="center"><?php echo ($rows['RecDateDownload']?$rows['RecDateDownload']:'-'); ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>



