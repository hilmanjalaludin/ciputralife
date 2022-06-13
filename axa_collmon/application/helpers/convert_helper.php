<?php 

// ------------------------------------------------------------------------

if ( ! function_exists('ConvertDateToEnglish'))
{
	/**
	 * date indo format to english format
	 */
	function ConvertDateToEnglish( $date="00-00-0000" )
	{
		
		$dNewDate = strtotime($date);
		if($date)
			return date('Y-m-d',$dNewDate);
	}
}

if ( ! function_exists('ConvertDateToIndo'))
{
	/**
	 * date english format to indonesia format
	 */
	function ConvertDateToIndo( $date="0000-00-00" )
	{
		$dNewDate = strtotime($date);
		if($date)
			return date('d-m-Y',$dNewDate);
	}
}