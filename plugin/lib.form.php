<?php
	//include("lib.tempelate.php");
	class jpForm extends mysql{
		
	 /** text input form **/
	 
		public function jpInput($name="",$css="",$value="",$js="",$true=false,$maxLength=0){
		
			echo "<input type=\"text\" id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\"
					".($js==''?'':$js)." value=\"".($value==''?'':$value)."\" 
					".($maxLength?"maxlength='$maxLength'":"")."
					".($true?'readonly':'').">";
					
		}
	public function jpField($name="",$css="",$value="",$js="",$true=false,$maxLength=0){
		
			$datas = "<input type=\"text\" id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\"
					".($js==''?'':$js)." value=\"".($value==''?'':$value)."\" 
					".($maxLength?"maxlength='$maxLength'":"")."
					".($true?'readonly':'').">";
					
			return $datas;	
		}	
	 /** input password **/
	 
		function jpPassword($name="",$css="",$value="",$js=""){
		
			echo "<input type=\"password\"  id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\">";
		}
		
		
	 /** submit button **/	
	 
		function jpSubmit($name="",$css="",$value="",$js=""){
		
			echo "<input type=\"submit\"  id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\">";
		}
		
	 /** submit button **/		
	 
		function jpButton($name="",$css="",$value="",$js=""){
		
			echo "<input type=\"button\"  id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\">";
		}
		
	 function jpCombo($name="",$css="", $data="", $value='',$js="", $true=false){
			echo "<select name=\"".($name==''?'':$name)."\" id=\"".($name==''?'':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." ".($true?'disabled="true"':'')." >";
				echo "<option value=''>--Choose--</option>";
				
				if(is_array($data)){
					foreach($data as $index=>$val){
						if( is_array($val)){
							foreach($val as $k=>$v)
							{
								if( (($k==$value) || ($v==$value)) && ($value!='') ) 
									echo "<option value=\"".$k."\" selected>".$v."</option>";
								else 
									echo "<option value=\"".$k."\" >".$v."</option>";
							}	
						}
						else{
							if( (($index==$value) || ($val==$value)) && ($value!='') ) 
								echo "<option value=\"".$index."\" selected>".$val."</option>";
							else 
								echo "<option value=\"".$index."\" >".$val."</option>";
						}
					}
				}
				else echo "<option value=\"".($data==''?'':$data)."\">".($data==''?'':$data)."</option>";
		    echo "</select>";
		}
		
	/** return string **/
	
		function jpSelect($name="",$css="",$data="",$value='',$js="", $true=false)
		{
			$select = "<select name=\"".($name==''?'':$name)."\" id=\"".($name==''?'':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." ".($true?'disabled':'')." >";
			    $select.=  "<option value=''>--Choose--</option>";	
					if(is_array($data)){
						foreach($data as $index=>$val){
							if( is_array($val)){
								foreach($val as $k=>$v)
								{
									if( (($k==$value) || ($v==$value)) && ($value!='') ) 
										$select.="<option value=\"".$k."\" selected>".$v."</option>";
									else 
										$select.="<option value=\"".$k."\" >".$v."</option>";
								}	
							}
							else{
								if( (($index==$value) || ($val==$value)) && ($value!='') ) 
									$select.="<option value=\"".$index."\" selected>".$val."</option>";
								else 
									$select.="<option value=\"".$index."\" >".$val."</option>";
							}
						}
					}
					else $select.="<option value=\"".($data==''?'':$data)."\">".($data==''?'':$data)."</option>";
				$select.= "</select>";
					
			return $select;
		}
				
		
	/** selected multiple **/
			
		function jpMultiple($name="",$css="",$data="",$value="",$js=""){
			echo "<select name=\"".($name==''?'':$name)."\" id=\"".($name==''?'':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." multiple=true>";
				if(is_array($data)){
					foreach($data as $index=>$val){
						if( (in_array($index,$value) || in_array($val,$value)) && ($value!='') ) {
							echo "<option value=\"".$index."\" selected>".$val."</option>";
						}
						else { echo "<option value=\"".$index."\" >".$val."</option>"; }
					}
				}
				else echo "<option value=\"".($data==''?'':$data)."\">".($data==''?'':$data)."</option>";
		    echo "</select>";
				
		}
		
		
	/** textarea **/
		
		function jpTextarea($name="",$css="",$value="",$js=""){
			if($css!='') 
				$css=$css;
			else 
				$css="style=\"width:160px;height:80px;\"";
			
			echo "<textarea name=\"".($name!=''?$name:'')."\" id=\"".($name!=''?$name:'')."\" ".($css!=''?"class='".$css."'":'')." ".($js!=''?$js:'').">".($value!=''?$value:'')."</textarea>";
		
		}
	/** hidden type **/
	
		function jpHidden($name='',$value){
			$form = "<input type='hidden' name='".($name?$name:'')."' id='".($name?$name:'')."' value='".($value?$value:'')."'>";
			if( $form ) echo $form;
			
		}
	
	/** radio button **/		
	 
		function jpRadio($name="", $css="", $value="", $js="", $attr=false, $lbl="", $dis=""){
			if($attr){
				echo "<input type=\"radio\" ".($dis?'disabled':'')." id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\" checked=\"true\">&nbsp;".($lbl==''?'':$lbl);
			}
			else{
				echo "<input type=\"radio\" ".($dis?'disabled':'')." id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" class=\"".($css==''?'default':$css)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\">&nbsp;".($lbl==''?'':$lbl);
			}
		}
		
		
		/** radio button **/		
		
		function jpCheck($name="",$lbl="", $value="",$js="",$attr=false, $dis=0){
			
			echo "<input type=\"checkbox\"  id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\" ".($attr?'checked':'')." ".($dis==1?'disabled=true':'').">&nbsp;".($lbl==''?'':$lbl);
		}
		
		function jpResultCheck($name="",$lbl="", $value="",$js="",$attr=false, $dis=0){
			
			return "<input type=\"checkbox\"  id=\"".($name==''?'txtFrm':$name)."\" name=\"".($name==''?'txtFrm':$name)."\" ".($js==''?'':$js)." value=\"".($value==''?'':$value)."\" ".($attr?'checked':'')." ".($dis==1?'disabled=true':'').">&nbsp;".($lbl==''?'':$lbl);
		}
		

		
	/** link **/
		function jpLink($name="", $css="", $data="", $value="", $js=""){
			echo "<a href=\"".($data!=''?$data:'')."\" name=\"".($name!=''?$name:'')."\" id=\"".($name!=''?$name:'')."\" ".
			" class=\"".($css!=''?$css:'default')."\" ".($js!=''?$js:'')." >".($value!=''?$value:'')."</a>";
		}
		
		
	/** lsit combo ***/
		function jpListcombo($name = NULL, $label = 'CheckAll', $data = array(),$values = NULL, $js = NULL,$attr = false, $dis=0)
		{
			echo "<fieldset style=\"background-color:#FFFFCC;border:1px solid #eeeeee;width:200px;\">
					<legend><a href=\"javascript:void(0);\" style=\"text-decoration:none;\" onclick=\"doJava.checkedAll('".$name."');\"># $label</a></legend>
					<div style=\"height:100px;overflow:auto;background-color:#FFFFCC;border:0px solid #eeeeee;\">
						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"90%\">";
						foreach($data as $key => $value )
						{
							echo "<tr>
									<td style=\"border-bottom:1px solid #eeeeee;\" width=\"5%\"><input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"$key\" ".(in_array($key,array_values($values))?'checked':'')."></td>
									<td style=\"border-bottom:1px solid #eeeeee;\">$value</td>
								</tr>";
						
						}
							echo "</table>";
				echo "</div>
					</fieldset>";			
		}
	}
	if(!is_object($jpForm)) $jpForm=new jpForm(true);