<?php
	class Menu extends mysql{
		function __construct(){
			parent::__construct();
		}
		
	//	function destruct(){}
		function getAllMenu()
		{	
			$_menu = array();
			
			$sql = "select a.* from tms_agent_profile a where a.id = '".$this->getSession('handling_type')."'";
			
			$qry = $this -> query($sql);
			$data = explode(",", $qry -> result_get_value('menu'));
			// print_r($data);
			$i = 0;
			foreach($data as $k => $Id )
			{
				$sql = "select a.el_id, a.file_name from tms_application_menu a where a.id='$Id' and a.flag = 1";
				$qry = $this -> query($sql);
				if( !$qry -> EOF() 
					AND !is_null($qry->result_get_value('file_name')) )  
				{
					
					$_menu[$i]= array (
						'id'	=> $qry->result_get_value('el_id'), 
						'name'	=> $qry->result_get_value('file_name')
					);
					
					$i++;
				}
				
			}
			
			return json_encode($_menu);
		}
		
		function cmenu($cat='', $menu='', $group_menu=''){	
			$xmenu = explode(',',$menu);
			$str = "<h3><a href=\"javascript:void(0)\">".$cat."</a></h3>";
            $str .= "<ul>";
				$vArr = $this -> valueSqlLoop("	SELECT 	id,menu,file_name,el_id ,updated_by FROM tms_application_menu 
												WHERE group_menu = '".$group_menu."' and flag=1 order by OrderId ASC");
				
				foreach($vArr as $vMenu){
					$str .= "<li>";
						if(in_array($vMenu[0],$xmenu)):
							$str .= "<a href=".$vMenu[2]."  id='".$vMenu[3]."' class=\"cssmenus\">".$vMenu[1]."</a>";
						endif;
					$str .= "</li>";
				}
			$str .= "</ul>";
			return $str;
		}
		
		
		function getAuxReason(){
			$datas = '';
			$sql =" select a.reasonid, a.reason_desc from cc_reasons a ";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this -> fetchrow($qry)){
				$datas[$row -> reasonid] = $row -> reason_desc;
			}
			 return "[".json_encode($datas)."]";
		}
		
		function assignMenuGroup(){
			$result  = array();
			$sql = "SELECT menu_group from tms_agent_profile where id='".$this->getSession('handling_type')."'";
			$menu = $this -> valueSQL($sql);
		
			if( $menu ){
				$result = explode(",",$menu);
			}
		
			return $result;	
		}
		
		function getMenuGroup(){
			$assignMenu = $this -> assignMenuGroup();
		
			$sql = "select a.GroupName, a.GroupId from tms_group_menu a
					where a.GroupShow=1 ORDER BY a.GroupOrder ASC";
					
			$qry = $this -> execute($sql,__FILE__,__LINE__);		
			while( $row = $this -> fetchrow($qry)):
				$datas[$row -> GroupName] = $row -> GroupId;
			endwhile;
			
			
			foreach($datas as $key => $value){
				foreach($assignMenu as $asg => $group){
					if( $value==$group ):
						$data[$key] = $value;
					endif;
				}
			}
			return $data;
		}
		
		private function createChat(){ ?> 
			<div id="accordions" style="border:0px solid #000;width:200px;">
				<h3><a href="javascript:void(0)">Chat Friend List</a></h3>
                <ul class="chat" style="height:100px"></ul>
            </div>
		<?php }		
				
				
		function iniateMenuUser(){ ?> 
			<div id="accordion" border="1px solid #000000;">
				<?php if ( $this -> getSession('user_profile')==200): ?>
								<h3><a href="javascript:void(0)">My Dashbord</a></h3>
								 <ul style="padding-left:0px;padding-top:3px;padding-right:0px;padding-bottom:0px;">
							<div id="container" style="width: 179px;height:180px;  margin-top:3px;margin-left:0px;margin: 0 auto ;border:0px solid #000;"></div>	
						</ul>
				<?php endif;
						$aCat = $this -> getMenuGroup(); 
						foreach($aCat as $category => $cIn):
							echo $this -> cmenu($category, $this -> getMenuByUser(), $cIn);
						endforeach;
				?>
							<!--<h3><a href="javascript:void(0)">SYSTEM</a></h3>
							<ul >
								<li><a href="javascript:void(0);" onClick="UserLogOut();">Logout</a></li>
							</ul> -->
			</div>
			<?php $this -> createChat(); ?>
					
		<?php	
		}
		
		function getMenuByUser(){
			$sql = " select a.menu from tms_agent_profile a where a.id='".$this->getSession('handling_type')."' ";
			$menu = $this -> fetchval($sql,__file__,__line__);
			if( $menu ) : return $menu; endif;
			
		}
	}
	
	if( !is_object($MainMenu) ): $MainMenu = new Menu();
	else:
		$MainMenu = new Menu();
	endif;
	
	
?>