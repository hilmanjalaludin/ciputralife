<?
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../fungsi/db_connect.php");
	require('../sisipan/parameters.php');
	
	//Get Sessions
	$username       = getSession("username");
	
	//Customer Data Table
	function showConfiguration() {
	  global $username, $V_UI_THEMES;
?>
    
  		<div class="content_table">
      	  <fieldset>
      	    <legend>Configuration</legend>
          	<table width="100%" class="activity">
          	<form name="frmAction">
          		<tr height="10">
          			<td width="100" nowrap></td>
          			<td nowrap></td>
          		</tr>
          		<tr>
          			<td nowrap>&nbsp;Themes</td>
          			<td nowrap>&nbsp;
          			  <select id="application_theme" name="optApplicationThemes">
      				      <option value="">(select themes...)</option>
        				    <?php
        				      $qry_search_str = "SELECT id, name FROM tms_application_themes";
        				      $qry_search_res = execSQL($qry_search_str);
        				      while($qry_search_rec = mysql_fetch_array($qry_search_res)) {
        				        echo "<option value=\"".$qry_search_rec["id"]."\"";
        				        if($qry_search_rec["id"]==$V_UI_THEMES)
        				          echo " selected";
        				        echo ">".$qry_search_rec["name"]."</option>";
        				      }
        				    ?>
      				    </select>
  
          			</td>
          		</tr>
          		<tr>
          			<td width="100" nowrap></td>
          			<td nowrap>&nbsp; <input type="button" value="Save" onclick="javascript:actSaveConfig(); parent.location.reload();" class="form_save"></td>
          		</tr>
          	</form>
          	</table>
          </fieldset>
      </div>

 <?  
	};

	showConfiguration();
	 
?>
