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
		
		
		function IFpage($param=''){
			if( $_REQUEST[$param]=='') :
				$this -> perpage = 0;
			else :
				$this -> perpage = 10;
			endif;
		}
		
		function IFpageX($param=''){
			if( $_REQUEST[$param]=='') :
				$this -> perpage = 0;
			else :
				$this -> perpage = 1;
			endif;
		}
		
		private function setStart(){
			if(empty($this -> pages) ):
				$this -> start = 0;
			else :
				$this -> start = ((($this -> pages)-1) * ($this -> perpage));
			endif;
		}
		
		function GroupBy($columns=''){
			if( $columns!=''){
				$this -> query.= " Group By ".$columns;
			} 
		}
		
		
		function OrderBy($columns='', $Type=''){
			if( $columns!=''){
				$this -> query.= " Order By  ".$columns." ". $Type;
			}
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
		
		function result()
		{
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
		
		function getTotPages()
		{
			$totalRows = $this -> getTotRows(); 
			
			if( $totalRows >0 ):
				$cellQuery = ceil(($totalRows)/($this -> perpage));  
			else:
				$cellQuery = 1;
			endif;
			return int($cellQuery);
		}
		
		function getSQL()
		{	
			if( !empty($this ->query))
			{
				return $this ->query;
			}		
		}
	}
	
 /** create object **/
	
	$ListPages = new ListPages()
	
?>