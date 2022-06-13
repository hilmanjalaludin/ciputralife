<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
** project date 10-02-2017
** Calculator untuk scoring
** Tambahkan methode untuk formula excel yang tak terjangkau
** Created By : Fajar
**/
class CalculatorScoring
{
	private $score = 0;
	private $poin = array();
	private $param = array();
	
	private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }
	
	public function ResetCalculator()
	{
		$this->score=0;
		$this->poin=array();
		return $this;
	}
	private function ResetPoin()
	{
		$this->poin=array();
	}
	
	public function SetPoin( $poin )
	{
		if(is_array($poin))
		{
			$this->poin=$poin;
		}
		else
		{
			$this->poin[]=$poin;
		}
		return $this;
	}
	
	public function SetParameter( $parameter )
	{
		$this->param=$parameter;
		return $this;
	}
	
	public function GetScore()
	{
		return $this->score;
	}
	
	public function AddScore($poin)
	{
		$this->score += $poin;
		return $this;
	}
	public function CalculateCriteriaPoin()
	{
		$sumpoin=0;
		if(isset($this->param['passpoin']) && isset($this->param['getmaxpoin']) && isset($this->param['getminpoin']) )
		{
			foreach($this->poin as $index => $value )
			{
				$sumpoin = $sumpoin + $value;
			}
			if($sumpoin == $this->param['passpoin'])
			{
				$this->score += $this->param['getmaxpoin'];
			}
			else
			{
				$this->score += $this->param['getminpoin'];
			}
			$this->ResetPoin();
		}
		return $this;
	}
	
	/**
	 * check string as same
	 *	return point
	 */
	public function PoinEqualString( $string_input="" )
	{
		$point=0;
		$sensitive="no";
		
		if(isset($this->param['strsource']))
		{
			if(isset($this->param['strsensitive']))
			{
				$sensitive = strtolower($this->param['strsensitive']);
			}
			if($sensitive==="no")
			{
				if(strcasecmp($string_input,$this->param['strsource'])===0)
				{
					$point=1;
				}
			}
			else
			{
				if(strcmp($string_input,$this->param['strsource'])===0)
				{
					$point=1;
				}
			}
		}
		
		$this->score+=$point;
		return $this;
	}
	
	
	
	
	
}