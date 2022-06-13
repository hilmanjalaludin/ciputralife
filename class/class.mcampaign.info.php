<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	
	class CampaignInfo extends mysql{
		var $CampaignNumber;
		var $action;
				
		function __construct()
		{
			parent::__construct();
			$this -> action = $this->escPost('action');
			$this -> CampaignNumber = $this->escPost('campaign_number');
		}
		
		function index()
		{
			if( $this->havepost('action'))
			{
				switch($this->action)
				{
					case 'calculation': $this -> getCallCulation(); break;
				}
			}
		}
		
		private function setQuery()
		{
			$sql = "select count(a.CustomerId) as TotalData,
						if( d.full_name is null,'IN ADMIN',d.full_name) as UserAss,
						SUM(IF(b.AssignAdmin IS NOT NULL AND b.AssignMgr IS NULL,1,0)) AS TotalInADMIN,
						SUM(IF(b.AssignMgr is not null,1,0)) as TotalInAM,
						SUM(IF(b.AssignMgr IS NOT NULL AND b.AssignSpv IS NULL,1,0)) AS TotalNotInSpv,
						SUM(IF(b.AssignSpv is not null,1,0)) as TotalInSpv,
						SUM(IF(b.AssignSelerId is not null and b.AssignSpv is not null,1,0 )) as AssignTM,
						SUM(IF(b.AssignSelerId is null and b.AssignSpv is not null,1,0 )) as NoAssignTM,
						SUM(IF(a.CustomerUpdatedTs IS NOT NULL AND a.CallReasonId IS NOT NULL and b.AssignSelerId IS NOT NULL AND b.AssignSpv IS NOT NULL,1,0)) as Utilize,
						SUM(IF(a.CustomerUpdatedTs IS NULL AND a.CallReasonId IS NULL AND b.AssignSelerId IS NOT NULL AND b.AssignSpv IS NOT NULL,1,0)) as UnUtilize
						from t_gn_customer a inner join t_gn_assignment b on a.CustomerId=b.CustomerId
						left join t_gn_campaign c on a.CampaignId=c.CampaignId
						left join tms_agent d on b.AssignSpv = d.UserId 
						where c.CampaignNumber='".$this->CampaignNumber."'
						group by b.AssignSpv ";
					
					
			return $sql;			

		} 
		
		function getCallCulation()
		{
			$qry = $this->execute($this ->setQuery(),__FILE__,__LINE__);
		?>
			<style>
				.custom{ padding-right:8px;}
				.footer{ padding-right:8px;color:blue;border-top:0px solid #dddddd; border-left:0px solid #dddddd;
					background-color:#fffccc;
					}
			</style>
			<div class="box-shadow" style="padding:8px;margin-top:15px;margin-bottom:15px;">
			<table border=0 width="90%" cellspacing=0 cellpadding=0>
				<tr>
					<th nowrap class="custom-grid th-first" style="font-weight:bold;" rowspan="2"> Level User </th>
					<!-- t h nowrap class="custom-grid th-middle" style="font-weight:bold;" rowspan="2"> Summary Data </th>
					<th nowrap class="custom-grid th-middle" style="font-weight:bold;" rowspan="2"> Not Assign To MGR </th>
					<th nowrap class="custom-grid th-middle" style="font-weight:bold;" rowspan="2"> Summary In MGR </th>
					<th nowrap class="custom-grid th-middle" style="font-weight:bold;" rowspan="2"> Not Assign To SPV </ t h>
					<th nowrap class="custom-grid th-middle" style="font-weight:bold;" rowspan="2"> Assign To SPV </t h -->
					<th nowrap class="custom-grid th-middle" style="font-weight:bold;" colspan="3"> Assign to TM  </th>
					<th nowrap class="custom-grid th-lasted" style="font-weight:bold;" rowspan="2"> Not Assign to TM </th>
				</tr>
				<tr>
					<th nowrap width="10%" class="custom-grid th-middle" style="font-weight:bold;"> Supply </th>
					<th nowrap width="10%" class="custom-grid th-middle" style="font-weight:bold;"> Solicited </th>
					<th nowrap width="10%" class="custom-grid th-middle" style="font-weight:bold;"> Not Solicited  </th>
				</tr>
			<?php 
				$totWe	= 0;
				$TotalInADMIN= 0;
				$TotalInAM= 0;
				$totalInSpv = 0;
				$totalNotInSpv = 0;
				$totalInTM = 0;
				$totalNotTM = 0;
				$totUtilize = 0;
				$totUnUtilize = 0;
				// echo "<pre>";
				// print_r($this ->fetchrow($qry));
				// echo "</pre>";
				while( $row = $this ->fetchrow($qry)){ ?>
				<tr>
					<td nowrap class="content-first" ><?php echo $row->UserAss; ?></td>
					<td nowrap class="content-middle custom" align="right"><?php echo $row->AssignTM; ?></td>
					<!-- t d nowrap class="content-middle custom" align="right"><?#php echo $row->TotalInADMIN; ?></td>
					<td nowrap class="content-middle custom" align="right"><?#php echo $row->TotalInAM; ?></td>
					<td nowrap class="content-middle custom" align="right"><?#php echo $row->TotalNotInSpv; ?></td>
					<td nowrap class="content-middle custom" align="right"><?#php echo $row->TotalInSpv; ?></t d>
					<td nowrap class="content-middle custom" align="right"><?#php echo $row->AssignTM; ?></t d -->
					<td nowrap class="content-middle custom" align="right"><?php echo $row->Utilize; ?></td>
					<td nowrap class="content-middle custom" align="right"><?php echo $row->UnUtilize; ?></td>
					<td nowrap class="content-lasted custom" align="right"><?php echo $row->NoAssignTM; ?></td>
				</tr>
			<?php 
				$totWe+= $row->TotalData;
				$TotalInADMIN += $row->TotalInADMIN;
				$TotalInAM += $row->TotalInAM;
				$totalInSpv += $row->TotalInSpv;
				$totalNotInSpv += $row->TotalNotInSpv;
				$totalInTM += $row->AssignTM; 
				$totalNotTM +=  $row->NoAssignTM;
				$totUtilize += $row->Utilize;
				$totUnUtilize += $row->UnUtilize;
				
			} ?>
			
				<tr> 
					<td class="content-first custom footer" ><b>Summary</b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?php echo $totalInTM;?></b></td>
					<!-- t d nowrap class="content-middle custom footer" align="right"><b><?#php echo $TotalInADMIN; ?></b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?#php echo $TotalInAM; ?></b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?#php echo $totalNotInSpv; ?></b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?#php echo $totalInSpv; ?></b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?#php echo $totalInTM; ?></b></t d -->
					<td nowrap class="content-middle custom footer" align="right"><b><?php echo $totUtilize; ?></b></td>
					<td nowrap class="content-middle custom footer" align="right"><b><?php echo $totUnUtilize; ?></b></td>
					<td nowrap class="content-lasted custom footer" align="right"><b><?php echo $totalNotTM; ?></b></td>
				</tr>
				</table></div>
				
		<?php
		}
		
	}
	
	$CampaignInfo = new CampaignInfo();
	$CampaignInfo ->index();
	
?>	