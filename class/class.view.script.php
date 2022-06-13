<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class viewScript extends mysql
	{
		function viewScript()
		{
			parent::__construct();
		}
		
		function main()
		{
			$this->header();
			$this->content();
		}
		
		function header()
		{
		?>
			<table width="100%" class="custom-grid" cellspacing="0">
			<thead>
				<tr height="20"> 
					<th nowrap class="custom-grid th-first">&nbsp;No</th>	
					<th nowrap class="custom-grid th-middle">&nbsp;Product </th>
					<th nowrap class="custom-grid th-middle">&nbsp;Script Title</th>
					<th nowrap class="custom-grid th-middle">&nbsp;Script File Name</th>
					<th nowrap class="custom-grid th-lasted">&nbsp;Read More</th>
				</tr>
			</thead>
		<?php
		}
		
		function content()
		{
		?>
			<tbody>
				<tr height="20"> 
					<th nowrap class="custom-grid th-first">&nbsp;No</th>	
					<th nowrap class="custom-grid th-middle">&nbsp;Product </th>
					<th nowrap class="custom-grid th-middle">&nbsp;Script Title</th>
					<th nowrap class="custom-grid th-middle">&nbsp;Script File Name</th>
					<th nowrap class="custom-grid th-lasted">&nbsp;Read More</th>
				</tr>
			</tbody>
		<?php
		}
	}
	
	$viewScript = new viewScript();
	$viewScript -> main();
?>