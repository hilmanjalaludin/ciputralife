<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../sisipan/parameters.php");
	require("../class/class.application.php");
	
	class viewPlan extends mysql
	{
		var $productId;
		
		function __construct()
		{
			parent::__construct();
			$this -> productId = $_REQUEST['productid'];
		}
		
		function getProduct()
		{
			$sql =" select a.ProductCode, a.ProductName from t_gn_product a where a.ProductId='".$this -> productId."' ";
			$datas = $this -> execute($sql,__FILE__,__LINE__);
			$rows = $this -> fetchrow($datas);
			return $rows;
		}
		
		function getPaymodePlan()
		{
			$sql =" select a.PayModeId, b.PayMode from t_gn_productplan a
					left join t_lk_paymode b on a.PayModeId=b.PayModeId
					where a.ProductId='".$this -> productId."' group by a.PayModeId ";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['PayModeId']] = array($rows['PayModeId'], $rows['PayMode']);			
			}
			return $datas;	
		}
		
		function getGroupPlan(){
			$sql =" select b.PremiumGroupDesc, a.PremiumGroupId from t_gn_productplan a
					left join t_lk_premiumgroup b on a.PremiumGroupId=b.PremiumGroupId
					where a.ProductId='".$this -> productId."' group by  a.PremiumGroupId order by b.PremiumGroupOrder ASC ";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['PremiumGroupId']] = array($rows['PremiumGroupId'],$rows['PremiumGroupDesc']);				
			}
			return $datas;	
		}
		
		function getPremium($array)
		{
			
			$sql =" select a.ProductPlanPremium from  t_gn_productplan a 
					where a.ProductId='".$this -> productId."'
					and a.PayModeId='".$array['paymode']."'
					and a.PremiumGroupId='".$array['group']."'
					and a.ProductPlan='".$array['plan']."'
					and a.ProductPlanName='".$array['planname']."'
					and a.ProductPlanAgeStart='".$array['agestart']."'
					and a.ProductPlanAgeEnd= '".$array['ageend']."'"; 
		//	echo $sql."<br>";
			
			$qry = $this -> query($sql);
			return $qry -> result_singgle_value();		
			
		}
		
		function getAgePlan($opt)
		{
			$sql =" select a.ProductPlanAgeStart, a.ProductPlanAgeEnd  from t_gn_productplan a
					where a.ProductId='".$this -> productId."'
					and a.PremiumGroupId=$opt group by a.ProductPlanAgeEnd";
			//echo $sql;			
			$qry = $this -> query($sql);
			//echo $sql;	//20 - 40
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = array($rows['ProductPlanAgeStart'],$rows['ProductPlanAgeEnd']);				
			}
			
			return $datas;	
		}
		
		function getPlan()
		{
			$sql =" select a.ProductPlan,a.ProductPlanName  from t_gn_productplan a where a.ProductId='".$this -> productId."' group by  a.ProductPlan";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProductPlan']] = $rows['ProductPlanName'];				
			}
			
			return $datas;	
		}
	}
	
	$viewPlan = new viewPlan();
	$paymode 	= $viewPlan -> getPaymodePlan(); 
	$groupBy 	= $viewPlan -> getGroupPlan();
	$getPlan 	= $viewPlan -> getPlan();
	$getProduct = $viewPlan -> getProduct();
	
	
	
?>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript">
		$(function(){ 
			$('#toolbars_plans').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Back']],
				extMenu  :[['BackToHome']],
				extIcon  :[['resultset_previous.png']],
				extText  :true,
				extInput :false,
				extOption:[]
			});
		});
		
	var BackToHome = function()
	{
		if(confirm('Do you want bacl to View Product Plans?'))
		{
			doJava.File = "mgt_viewplan_nav.php";
			doJava.Params = {action:'back'}
			extendsJQuery.Content();
		}
	}
			
				
		
</script>			
<div id="toolbars_plans" class="toolbars"></div>
<fieldset id="top-panel" style="border:1px solid #dddddd;margin-top:-20px;">
	<legend> <h3 style="color:red;padding-top:10px;"> <b style="color:#BBBEEE;">View Detail Product Plan</b>  - <?php echo $getProduct->ProductCode; ?> - <?php echo $getProduct->ProductName ?> </h3></legend>
	<div class="box-shadow" style="background-color:#FFFFFF;" cellspacing="0" >
	
		<table width="90%" align="center" >
		<?php
			foreach( $groupBy as $keyGroup=>$viwGroup ){
				?>
					<tr>
						<td>
							<h2 style="color:red;padding-top:10px;"><u><?php echo $viwGroup[1]; ?></u></h2> 
								<table width="99%" cellspacing="0" >
									
									<?php
										foreach($paymode as $keyMode=>$vwMode){
											?>
												<tr>
													<td>
														<fieldset style="border:1px solid:#dddddd;" class="corner" >
															<legend> <h3><?php echo $vwMode[1]; ?></h3></legend>
															<table  style="border:1px solid #dddddd;" cellpadding="8px;" width="100%" cellspacing="0">
																<tr>
																	<th colspan="2" style="text-align:center;border-right:1px solid #eeeeee;background-color:#7e7aaf;color:#FFFFFF;t">Age Band</th>
																	<?php
																				foreach( $getPlan as $keyPlan=>$vwPlan){	
																					?>
																					<th style="text-align:center;border-right:1px solid #eeeeee;background-color:#7e7aaf;color:#FFFFFF;"><?php echo $vwPlan; ?></th>
																					<?php
																				}	
																	?>
																</tr>
																<?
																	$getAgePlan = $viewPlan->getAgePlan($viwGroup[0]);
																	
																	$i = 0;
																	foreach( $getAgePlan as $keyAge=>$vwAge){
																		$color=($i%2!=0?'#FFFFFF':'#eeeeee');
																		
																		?>
																			<tr bgcolor="<?php echo $color;?>"> 
																				<td style="text-align:right;border-right:1px solid #dddddd;border-top:1px solid #dddddd;color:red;" width="2%"><?php echo $vwAge[0]; ?></td>
																				<td style="text-align:right;border-right:1px solid #dddddd;border-top:1px solid #dddddd;color:red;" width="2%"><?php echo $vwAge[1]; ?></td>
																				<?php
																					foreach( $getPlan as $keyPlan=>$vwPlan){
																						$array = array(
																							'paymode'=>$vwMode[0],
																							'group'=>$viwGroup[0],
																							'plan'=>$keyPlan,
																							'planname'=>$vwPlan,
																							'agestart'=>$vwAge[0],
																							'ageend'=>$vwAge[1]
																						);
																					
																						?>
																						<td style="text-align:right;border-right:1px solid #dddddd;border-top:1px solid #dddddd;"><b style="color:blue;">Rp. <?php echo formatRupiah($viewPlan->getPremium($array)) ; ?></b></td>
																						<?php
																					}	
																				?>
																				
																				
																			</tr>		
																		<?php
																		
																		$i++;
																	}
																?>
															</table>
															</fieldset>	
													
													</td>
												</tr>	
											<?php
										}
									?>
								</table>	
						</td>
					</tr>
					
				<?php	
			}
			
		?>
		</table>
		
	</div>
</fieldset>