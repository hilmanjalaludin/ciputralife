<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="qa_scoring">
    <meta name="author" content="dzar">

    <title>Coll Monitoring</title>
	
	<!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url("assets/vendor/bootstrap/css/bootstrap.min.css")?>" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo base_url("assets/vendor/metisMenu/metisMenu.min.css")?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url("assets/sbadmin/css/sb-admin-2.css")?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo base_url("assets/vendor/font-awesome/css/font-awesome.min.css")?>" rel="stylesheet" type="text/css">
	
	
	
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

	 <!-- jQuery -->
    <script src="<?php echo base_url("assets/vendor/jquery/jquery.min.js")?>"></script>
	
	<script src="<?php echo base_url("assets/js_view/qa_collmon/js_qa_score.js")?>"></script>
	
</head>

<body>
	<?php 
		/**
		**tanem
		**/
		$APE = 2;
		$CusInfo = $CustomerInfo[0];
		$category = $Form['category'];
		$sub_category = $Form['sub_category'];
		$input_func = $Form['input_func'];
		$sub_category_input = $Form['sub_category_input'];
		$answer_label = $Form['answer_label'];
		$answer_default = $Form['answer_default'];
		$answer_is_session = $Form['answer_is_session'];
		$answer_is_readonly = $Form['answer_is_readonly'];
		$add_remark_category = $Form['add_remark_category'];
		$score_place = array_chunk($score_place,3,true);
		// echo"<pre>";
		// print_r($score_place);
		// echo"</pre>";
	?>
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
				<?php echo(isset($Form['header']['name'])?$Form['header']['name']:"QA SCORING / CALL MONITORING / SALES QUALITY") ?>
				</h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<form role="form" id="score_form">
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Form Scoring
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-12">
								<div class="well">
								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<label>TM</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_tm",
														'name'=>"mon_tm",
														'value'=>$CusInfo['id'],
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>QA Callmon</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_qa",
														'name'=>"mon_qa",
														'value'=>$this->session->userdata('username'),
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>Customer</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_customer",
														'name'=>"mon_customer",
														'value'=>$CusInfo['CustomerFirstName'],
														'disabled'=>""
												));
											?>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label>Campaign Name</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_campaign",
														'name'=>"mon_campaign",
														'value'=>$CusInfo['CampaignName'],
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>Product Code</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_prod_code",
														'name'=>"mon_prod_code",
														'value'=>$CusInfo['ProductCode'],
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>Product Name</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_prod_name",
														'name'=>"mon_prod_name",
														'value'=>$CusInfo['ProductName'],
														'disabled'=>""
												));
											?>
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<label>Date Selling</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_date_sale",
														'name'=>"mon_date_sale",
														'value'=>$CusInfo['SELLINGDATE'],
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>Date Callmon</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_date",
														'name'=>"mon_date",
														'value'=>date('d-m-Y H:i:s'),
														'disabled'=>""
												));
											?>
										</div>
										<div class="form-group">
											<label>Recording Duration</label>
											<?php 
												echo form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"mon_rec_dur",
														'name'=>"mon_rec_dur",
														'value'=>$recording['time_format'],
														'disabled' => ""
												));
											?>
										</div>
									</div>
								</div>
								</div>
							</div>
						</div>
						
						
						<div class="row">
							<div class="col-lg-12">
								<?php
									$no=1;
									$form_qa="";
									$text="";
									$readonly=array();
									foreach($category as $id_category => $category_desc){
										$form_qa .= '<div class="panel panel-default">
												<div class="panel-heading"> #'.$category_desc.' </div>
												<div class="panel-body">
												<div class="table-responsive">
												<table class="table table-hover">';
										foreach($sub_category[$id_category] as $id_sub=> $sub_desc){
											$form_qa .='<tr id="mark_score_'.$id_sub.'" >
														<td width="1%" >'.$no.'.</td>
														<td>'.$sub_desc.'</td>
														<td width="40%">';
											
											if($sub_category_input[$id_category][$id_sub])
											{
												// var_dump($answer_is_session);
												if($answer_is_session[$id_sub])
												{
													$index_session=reset($answer_label[$id_category][$id_sub]);
													$text = $this->session->userdata($index_session);
												}
												else
												{
													$text=(isset($saved['coll_form_input'][$id_sub]['id'])?
														$saved['coll_form_input'][$id_sub]['value'] : ""
													);
												}
												
												switch ($input_func[$id_category][$id_sub]) {
												
													case "textarea":
														$form_qa .= form_textarea(array(
															"name" =>"qa_quest_".$id_sub,
															"id " => "qa_quest_".$id_sub,
															"class" => "form-control",
															"rows"=>"2",
															"placeholder"=>reset($answer_label[$id_category][$id_sub]),
															'value'=> $text
														));
														break;
														
														case "textbox":
														$attribute=array();
														$attribute=array(
															'type'=>"text",
															'class'=>"form-control pull-right input-sm",
															'id'=>"qa_quest_".$id_sub,
															'name'=>"qa_quest_".$id_sub,
															'value'=> $text
														);
														if($answer_is_readonly[$id_sub])
														{
															$attribute=array(
																'type'=>"text",
																'class'=>"form-control pull-right input-sm",
																'id'=>"qa_quest_".$id_sub,
																'name'=>"qa_quest_".$id_sub,
																'value'=> $text,
																'readonly'=>"readonly"
															);
														}
														
														$form_qa .= form_input($attribute);
														break;
													default:
														$form_qa .= form_input(array(
															'type'=>"text",
															'class'=>"form-control pull-right input-sm",
															'id'=>"qa_quest_".$id_sub,
															'name'=>"qa_quest_".$id_sub,
														));
												}
											}
											else
											{
												$choose="";
												if(isset($saved['coll_result_form'][$id_sub]['value']))
												{
													$choose=$saved['coll_result_form'][$id_sub]['value'];
												}
												else
												{
													$choose=$answer_default[$id_category][$id_sub];
												}
												switch ($input_func[$id_category][$id_sub]) {
												
													case "combobox":
														$form_qa .= form_dropdown('qa_quest_'.$id_sub, 
																	$answer_label[$id_category][$id_sub],
																	$choose,
																	"id=\"qa_quest_".$id_sub."\" class=\"form-control input-sm\" "
																);
														break;
													default:
														$form_qa .= form_dropdown('qa_quest_'.$id_sub, 
																	array(),
																	"",
																	"id=\"qa_quest_".$id_sub."\" class=\"form-control input-sm\" "
																);
												}
											}
											$form_qa .= '</td></tr>';
											$no++;
										}
										$form_qa .='</table><!-- /table -->
												</div><!-- /.table-responsive -->';
										if($add_remark_category[$id_category])
										{
											$text=(isset($saved['coll_category_remarks'][$id_category]['id'])?
												$saved['coll_category_remarks'][$id_category]['value'] : ""
											);
											$form_qa .= '<div class="row">
														<div class="col-lg-12">
															<div class="row">
															<div class="col-lg-6"></div>
															<div class="col-lg-6">'.
															form_textarea(array(
															  "name" =>"remark_".$id_category,
															  "id " =>"remark_".$id_category,
															  "class" => "form-control",
															  "rows"=>"3",
															  "placeholder"=>"Remark ".$category_desc,
															  'value'=> $text
															)).
															'</div> <!-- /.col-lg-6 -->
															</div> <!-- /.row -->
															</div> <!-- /.col-lg-12 -->
														</div><!-- /.row -->';
										}
										$form_qa .='</div><!-- /.panel-body -->
											</div>';
									}
									echo $form_qa;
								?>
								<div class="panel panel-default">
									<div class="panel-heading"> &nbsp; </div>
									<div class="panel-body">
										
										<?php 
											for($i=1;$i<2;$i++)
											{
												echo '<div class="form-group"><label>Remaks</label>'.
													form_textarea(array(
														  "name" =>"report_remaks_".$i,
														  "id " =>"report_remaks_".$i,
														  "class" => "form-control",
														  "rows"=>"3",
														  "placeholder"=>"Remarks",
														  "value"=>(
															isset($saved['coll_form_remark'][$i]['id'])?
															$saved['coll_form_remark'][$i]['value']:
															""
														  )
													)).
												'</div>';
											}
										?>
										
									</div>
								</div>
							</div>
						</div>
						
						<?php if($this->session->userdata('ProductGrupId')==$APE){ ?>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading"> &nbsp; </div>
									<div class="panel-body">
									<?php
										echo '<div class="form-group"><label>Note</label>'.
											form_textarea(array(
												  "name" =>"note_static",
												  "id " =>"note_static",
												  "class" => "form-control",
												  "rows"=>"3",
												  "placeholder"=>"Note",
												  "value"=>(
													isset($saved['coll_form_remark'][$i]['id'])?
													$saved['coll_form_remark'][$i]['value']:
													""
												  )
											)).
										'</div>';
									?>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">&nbsp;</div>
									<div class="panel-body">
									<?php
										echo '<div class="form-group"><label>Status ALL System & Rec. Result</label>'.
											form_textarea(array(
												  "name" =>"status_system_static",
												  "id " =>"status_system_static",
												  "class" => "form-control",
												  "rows"=>"3",
												  "placeholder"=>"....",
												  "value"=>(
													isset($saved['coll_form_remark'][$i]['id'])?
													$saved['coll_form_remark'][$i]['value']:
													""
												  )
											)).
										'</div>';
									?>						
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">&nbsp;</div>
									<div class="panel-body">
									<?php
										echo '<div class="form-group"><label>Plan (Polis/TT)</label>'.
											form_input(
												array(
													'type'=>"text",
													'class'=>"form-control pull-right input-sm",
													'id'=>"plan_static",
													'name'=>"plan_static"
												)
											).
										'</div>';
									?>						
									</div>
								</div>
							</div>
						</div>
					<?php }?>
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading"> Score Review </div>
									<div class="panel-body">
									<?php 
										foreach($score_place as $rows=>$score_cols)
										{
											echo '<div class="row">';
											foreach($score_cols as $id_calculation=>$score_text)
											{
												echo '<div class="col-lg-4">
												<div class="form-group">
													<label>'.$score_text.'</label>'.
													form_input(array(
														'type'=>"text",
														'class'=>"form-control pull-right input-sm",
														'id'=>"score_".$id_calculation,
														'name'=>"score_".$id_calculation,
														'value'=>(
															isset($saved['coll_calculation_result'][$id_calculation]['id'])?
															$saved['coll_calculation_result'][$id_calculation]['value']:
															"0"
														),
														'readonly'=>"readonly"
												)).'</div></div>';
											}
											echo '</div>';
										}
									?>								
									</div>
								</div>
							</div>
						</div>
						<button class="btn btn-primary" type="button" id="btnCalScore">Calculate Score</button>
						<button class="btn btn-primary" type="button" id="btnSaveScore">Save Score</button>
					</div>
				</div>
			</div>
		</div>
	<!-- /.row -->
		</form>
	</div>
	<!-- /.container -->
	
    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo base_url("assets/vendor/bootstrap/js/bootstrap.min.js")?>"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo base_url("assets/vendor/metisMenu/metisMenu.min.js")?>"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo base_url("assets/sbadmin/js/sb-admin-2.js")?>"></script>
	
	<script type="text/javascript">
	$(function() {
	
		QaScore.SetSource({
			'tools' : "<?php echo site_url('qa_collmon/json_tools'); ?>",
			'send'  : "<?php echo site_url('qa_collmon/save_score_fpa'); ?>",
			'score'  : "<?php echo site_url('qa_collmon/json_score_review_fpa'); ?>"
		});
		$( "#btnSaveScore" ).click( function(){
			QaScore.SubmitAction();
		});
		
		$( "#btnCalScore" ).click( function(){
			QaScore.CalculateScore();
		});
	});
	</script>
</body>

</html>
