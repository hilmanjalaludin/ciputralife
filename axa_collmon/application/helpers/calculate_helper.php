<?php 

// ------------------------------------------------------------------------

if ( ! function_exists('criteria_poin'))
{
	/**
	 * Integer to "Rupiah"
	 */
	function criteria_poin( $pre_poin, $high_poin, $max_score, $min_score )
	{
		$getPoin=0;
		$sumpoin=0;
		if(is_array($pre_poin))
		{
			foreach($pre_poin as $index => $value )
			{
				$sumpoin = $sumpoin + $value;
			}
			if($sumpoin == $high_poin)
			{
				$getPoin = $max_score;
			}
			else
			{
				$getPoin = $min_score;
			}
		}
		
		return $getPoin;
	}
}

if ( ! function_exists('poin_equal_string'))
{
	/**
	 * check string as same
	 *	return point
	 */
	function poin_equal_string( $string1, $string2, $sensitive="NO" )
	{
		$point=0;
		if($sensitive==="NO")
		{
			if(strcasecmp($string1,$string2)===0)
			{
				$point=1;
			}
		}
		else
		{
			if(strcmp($string1,$string2)===0)
			{
				$point=1;
			}
		}
		return $point;
	}
}