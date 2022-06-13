<?php
	require("class/MYSQLConnect.php");
	
	class recover_menu extends mysql
	{
		function recover_menu()
		{
			echo "starting recover menu ...\n\n";
		}
		
		function start()
		{
			$sql = "select a.id, a.menu from tms_application_menu a
					where a.el_id is null";
			
			$qry = $this->query($sql);
			
			foreach($qry->result_assoc() as $rows)
			{
				$datas[$rows['id']] = $rows['menu'];
			}
			$arr = $this->convertName($datas);
			
			foreach($arr as $key => $value)
			{
				$sql = "UPDATE tms_application_menu SET el_id = '".$value."' WHERE id = '".$key."'";
				$qry = $this->query($sql);
				
				if($qry->query())
				{
					echo "update el_id ".$value.", success!\n";
				}
				else{
					echo "update el_id ".$value.", failure!\n";
				}
				// $update['el_id'] = $value;
				// $where['id'] = $key;
				
				// if($this->set_mysql_update('tms_application_menu',$update,$where))
				// {
					// echo "update el_id, success!\n";
				// }
				// else{
					// echo "update el_id, failure!\n";
				// }
				// echo $this->sqlText;
			}
		}
		
		function convertName($arr)
		{
			if(is_array($arr))
			{
				foreach($arr as $key => $value)
				{
					$data[$key] = str_replace("-","",str_replace(")","",str_replace("(","",strtolower(str_replace(" ","_",$value)))));
				}
				
				return $data;
			}
		}
	}
	
	$recover_menu = new recover_menu();
	$recover_menu->start();
?>