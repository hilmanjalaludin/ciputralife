<?php
	class ListPages extends mysql{
		
		var $query;
		var $where;
		var $perpage;
		var $start;
		var $pages;
		var $result;
		
		
		function __construct(){
			
			parent::__construct();
			
			$this -> query   = '';
			$this -> where   = '';
			$this -> perpage = '';
			$this -> pages   = '';
			$this -> result  = FALSE;
		}
		
		function setPage($cnt=0){
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
		
		function setLimit(){
			$this -> setStart();
			$this -> query.= ' LIMIT '.$this->start.','.$this->perpage.'';
		}
		
		function setWhere($where){
			$this -> where = ' WHERE 1=1 ';	
			
			if($where):
				$this -> where.= $where; 
			endif;
			
			$this -> query.= $this -> where;
			
		}
		
		function query($sql=''){
			if(!empty($sql)) :	
				$this -> query = $sql;
			endif;	
		}
		
		function result(){
			if( !empty($this ->query)):
				$qry = $this ->execute($this -> query,__FILE__,__LINE__);
			endif;
			
			$this -> result = $qry;
		}
		
		function getTotRows(){
			$res = $this -> execute($this -> query,__FILE__,__LINE__);
			$res = $this -> numrows($res);
			if ( $res) : return $res; endif;	
		}
		
		function getTotPages(){
			$totalRows = $this -> getTotRows(); 
			
			if( $totalRows >0 ):
				$cellQuery = ceil(($totalRows)/($this -> perpage));  
			else:
				$cellQuery = 1;
			endif;
			return int($cellQuery);
		}
	}
	
 /** create object **/
 
	$ListPages = new ListPages()

?>