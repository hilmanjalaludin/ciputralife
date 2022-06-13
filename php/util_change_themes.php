<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	function UI_THEMES(){
		global $db;
			$sql = "select a.id as ThemesName, a.name as ThemesValue from tms_application_themes a ";
			if( is_object($db)):
				$qry = $db->execute($sql,__FILE__,__LINE__);
				while( $row = $db -> fetchrow($qry)):
					$datas[$row->ThemesName] = $row->ThemesValue;
				endwhile;
				
				return $datas;
			endif;
		return Array();	
	}
	
	
?>
	<!-- start : javascript -->
	<html>
	<head>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script>
		// $(function(){
			// $('.corner').corner();
		// });
		
		function saveThemes(){
			doJava.File   = '../class/class.updater.app.php'
			doJava.Params = {
				action:'active_themes',
				themes_name :doJava.SelText('themes_active'),
				themes_value: doJava.Value('themes_active')
			}
			
			if( confirm('Do you want to Change Themes') ){
				var error = doJava.Post();
				if( error ){
					window.location = window.location.href;
				}
			}
		}		
	</script>
	</head>
	<body>
	<!-- stop : javascript -->
	<div style="margin-left:4px;border:0px solid #ddd;height:200px;" class="top-content" >
		<fieldset class="corner" style="border:1px solid #ddd;">
			<legend class="icon-application">&nbsp; Update Themes  </legend>
		<div style="margin-left:4px;" class="subcontent">
			<table cellpadding="9px">
				<tr>
					<td> Active Themes</td>
					<td> <input type="text" readonly value="<?php echo $Themes->V_UI_THEMES; ?>" class="subcontent"> </td>
				</tr>
				<tr>
					<td> Change Theme</td>
					<td>
						<select name="themes_active" id="themes_active" style="border:1px solid #ddd;width:200px;">
							<?php foreach( UI_THEMES() as $valueId => $valueName ): ?>
								<option value="<?php echo $valueId;?>"><?php echo $valueName; ?></option>
							<? endforeach;?>
						</select>
					</td>
				</tr>	
				<tr>
					<td>&nbsp;</td>
					<td><a href="javascript:void(0);" class="sbutton" onclick="saveThemes();"><span>&nbsp;Save</span></a></td>
				</tr>	
			</table>
		</div>
		</legend>
	<div>	
	
	
	<!-- stop : contents -->	
	</body>
	</html>