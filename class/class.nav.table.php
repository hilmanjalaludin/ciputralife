<?php
	class NavPages extends mysql{
		
		var $query;
		var $where;
		var $perpage;
		var $start;
		var $pages;
		var $result;
		
		
		public $sesPages;
		function __construct(){
			
			parent::__construct();
			
			$this -> query   = '';
			$this -> where   = '';
			$this -> perpage = '';
			$this -> pages   = '';
			$this -> result  = FALSE;
		}
		
		function setPage($cnt=0){
			$this -> sesPages = $cnt;
			if( $cnt ):	
				$this -> perpage = $cnt;
			endif;
		}
		
		private function setStart(){
			if(empty($this -> pages) ):
				$this -> start = 0;
			else :
				$this -> start = ((($this -> pages)-1) * ($this -> perpage));
			endif;
		}
		
		
		function IFpage($param)
		{
			if( $this -> escPost($param)!=''){
				$this -> setSession('V_CMP',$this -> escPost($param));
				
				if( $_SESSION['V_CMP']!= $this -> escPost($param)){	
					unset($_SESSION['V_CMP']);
				}
			}
			else
			{
				if(isset($_SESSION['V_CMP'])){
					$this -> perpage = $this -> sesPages;	
				}
				else{
					$this -> perpage = 0;
				}
			}
		}
		
		
		function GroupBy($columns=''){
			if( $columns!=''){
				$this -> query.= " Group By ".$columns;
			} 
		}
		
		
		
		
		function OrderBy($columns='', $Type='ASC'){
			if( $columns!=''){
				$this -> query.= " Order By  ".$columns." ". $Type;
			}
		}
		
		function setWhere($where){
			$this -> where = ' WHERE 1=1 ';	
			$this -> where.= $where; 
			$this -> query.= $this -> where;
			
		}
		
		function echo_query()
		{
			echo "<pre>";
			echo $this -> query;
			echo "</pre>";
		}
		
		function query($sql=''){
			if(!empty($sql)) :	
				$this -> query = $sql;
			endif;	
		}
		
		function result(){
			if( !empty($this -> query)):
				$qry = $this -> execute($this -> query,__FILE__,__LINE__);
			endif;
			
			$this -> result = $qry;
		}
		
		function getTotRows(){
	
			$res = $this -> execute($this -> query,__FILE__,__LINE__);
			$jml = $this -> numrows($res);
			if ($jml >0 ) : return $jml; 
			else :
				return 0;
			endif;	
		}
		
		function getTotPages(){
			$totalRows = $this -> getTotRows(); 
			
			if( $totalRows >0 ):
				$cellQuery = ceil(($totalRows)/($this -> perpage));  
			else:
				$cellQuery = 1;
			endif;
			return $cellQuery;
		}
	}
	
 /** create object **/
 
	$NavPages = new NavPages()

?>