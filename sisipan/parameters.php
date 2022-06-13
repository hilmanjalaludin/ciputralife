<?php
	
/* reference website config **/

class Themes extends mysql 
{

 var $V_WEB_TITLE;
 var $V_WEB_VERSION;
 var $V_WEB_AUTHOR;
 var $V_WEB_COPYRIGHT;
 var $V_LOGO_DARK;
 var $V_LOGO_ORANGE;
 var $V_WEB_URL;
 var $V_WEB_FILE_PATH;
 var  $V_UI_THEMES;
	
/*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 */

private static $instance = null;
public static function &get_instance()
{
	if(is_null(self::$instance) ){
		self::$instance = new self();
	}
	 return self::$instance;
}

/*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 * @ return : void
 */

public function __construct()
{
	parent::__construct();
	$this -> initThemes();
}
	
/*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 * @ return : void
 */
 
function destruct(){}

/*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 * @ return : void
 */
 
function initThemes()
 {
	$this -> V_WEB_TITLE	 =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='TITLE'");
	$this -> V_WEB_VERSION	 =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='VERSION'");
	$this -> V_WEB_AUTHOR	 =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='AUTHOR'");
	$this -> V_WEB_COPYRIGHT =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='COPYRIGHT'");
	$this -> V_LOGO_DARK	 =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='LOGO_DARK'");
	$this -> V_LOGO_ORANGE	 =	$this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='WEBSITE' AND PARAM_NAME='LOGO_ORANGE'");
	$this -> V_WEB_URL		 =  $this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='CONFIG' AND PARAM_NAME='URL'");
	$this -> V_WEB_FILE_PATH =  $this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='CONFIG' AND PARAM_NAME='FILE_PATH'");
	$this -> V_UI_THEMES     =  $this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='CONFIG' AND PARAM_NAME='THEME'");
	$this -> V_PDF_DIRECTORY =  $this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='CONFIG' AND PARAM_NAME='PDF_DIRECTORY'");
	$this -> INSTRUCTION	 =  $this -> valueSQL("SELECT content FROM tms_application_config WHERE module_name='INSTRUCTION' AND PARAM_NAME='INSTRUCTION'");
	$this -> V_SYS_ICON      =  $this -> valueSQL("SELECT param_value FROM tms_application_config WHERE module_name='COMPANY' AND PARAM_NAME='COMPANY_LOGO'");
 }
 
 /*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 * @ return : void
 */
 
}

if(class_exists('Themes'))
{ 
	$Themes = new Themes();
}	
	
?>