<?php
class Convert extends mysql
{
	private $url;
	private $loc;
	
	function Convert()
	{
		$this->url = '192.168.16.6/recording/';
		$this->loc = '192.168.16.6/wav_recording/';
	}
	
	function getVocLoc($file_voc_loc)
	{
		$arr = explode('/',$file_voc_loc);
		return $this->url.$arr[5]."/".$arr[6]."/".$arr[7];
	}
	
	function changeExt($file,$ext)
	{
		$x = explode('.',$file);
		$y = $x[0];
		$z = $y.'.'.$ext;
		
		return $z;
	}
	
	// function execute($rec_filename, $wav_filename)
	// {
		// exec("chmod 777 \"$rec_filename\"");
		// exec("nice sox \"$rec_filename\" \"$wav_filename\"");
	// }
	
	function checkFileExt($file='',$ext='gsm')
	{
		if( $file!=''){
			$fn = explode('.', $file);
			if( $fn[count($fn)-1] == $ext ) 
			{
				return true;
			}
			else
			{
				return false;
			}
		}	
		else
			return false;
	}
}
?>
