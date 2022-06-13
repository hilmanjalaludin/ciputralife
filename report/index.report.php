<?php
error_reporting(1);

include(dirname(__FILE__).'/../sisipan/sessions.php');
include(dirname(__FILE__).'/../fungsi/global.php');
include(dirname(__FILE__).'/../class/MYSQLConnect.php');
include(dirname(__FILE__).'/../class/class.application.php');
include(dirname(__FILE__).'/../class/lib.form.php');
include(dirname(__FILE__).'/../sisipan/parameters.php');

define('level_user_admin',1);
define('level_user_manager',2);
define('level_user_spv',3);
define('level_user_agent',4);
define('user_state_active',1);
define('user_state_unactive',0);

class indexReport extends mysql
{
	function indexReport()
	{
		parent::__construct();
		$this->start();
	}
	
	function start()
	{
		if( $this ->havepost('report_type') )
		{
			$new_name_file = $_REQUEST['report_type'];
			if( !empty($new_name_file))
			{
				include(dirname(__FILE__).'/CONTENT/'.$new_name_file.'.php');
				$object = new $new_name_file();
				$object->show_content();
			}
		}
	}
}

new indexReport;
?>