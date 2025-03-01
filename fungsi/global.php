<?php

// error_reporting(~E_ALL);

function SetNoCache()
	{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1
	header("Pragma: no-cache"); // HTTP/1.0
	}

function setSession($param_name, $param_value)
	{
	if (isset($param_name)) unset($_SESSION[$param_name]);
	$_SESSION[$param_name] = $param_value;
	/*
	global ${$param_name};
	if(session_is_registered($param_name))
	session_unregister($param_name);
	${$param_name} = $param_value;
	session_register($param_name);
	*/
	}

function unsetSession($param_name)
	{
	if (isset($param_name)) unset($_SESSION[$param_name]);
	}

function getSession($param_name)
	{
	$param_value = "";
	if (!isset($_POST[$param_name]) && !isset($_GET[$param_name]) && isset($_SESSION[$param_name])) $param_value = $_SESSION[$param_name];
	return $param_value;
	}

function getParam($param_name)
	{
	$param_value = "";
	if (isset($_POST[$param_name])) $param_value = $_POST[$param_name];
	  else
	if (isset($_GET[$param_name])) $param_value = $_GET[$param_name];
	return $param_value;
	}

function leadZero($str_no, $amount)
	{
	for ($i = strlen($str_no); $i < $amount; $i++)
		{
		$str_no = '0' . $str_no;
		}

	return $str_no;
	}

function random($max)
	{
	srand((double)microtime() * 1000000);
	return rand(1, $max);
	}

function showDate()
	{
	$today = getdate();
	$today_date = $today["mday"];
	$today_no = $today["wday"];
	$today_month = $today["mon"];
	$today_year = $today["year"];
	switch ($today_no)
		{
	case 0:
		$day_name = "Minggu";
		break;

	case 1:
		$day_name = "Senin";
		break;

	case 2:
		$day_name = "Selasa";
		break;

	case 3:
		$day_name = "Rabu";
		break;

	case 4:
		$day_name = "Kamis";
		break;

	case 5:
		$day_name = "Jumat";
		break;

	case 6:
		$day_name = "Sabtu";
		break;
		}

	switch ($today_month)
		{
	case 1:
		$month_name = "Januari";
		break;

	case 2:
		$month_name = "Februari";
		break;

	case 3:
		$month_name = "Maret";
		break;

	case 4:
		$month_name = "April";
		break;

	case 5:
		$month_name = "Mei";
		break;

	case 6:
		$month_name = "Juni";
		break;

	case 7:
		$month_name = "Juli";
		break;

	case 8:
		$month_name = "Agustus";
		break;

	case 9:
		$month_name = "September";
		break;

	case 10:
		$month_name = "Oktober";
		break;

	case 11:
		$month_name = "November";
		break;

	case 12:
		$month_name = "Desember";
		break;
		}

	$date_now = "$day_name, $today_date $month_name $today_year";
	return $date_now;
	}

function evalDate($char_date)
	{
	$temp = explode("-", $char_date);
	$month_no = $temp[1];
	switch ($month_no)
		{
	case 1:
		$month_name = "Januari";
		break;

	case 2:
		$month_name = "Februari";
		break;

	case 3:
		$month_name = "Maret";
		break;

	case 4:
		$month_name = "April";
		break;

	case 5:
		$month_name = "Mei";
		break;

	case 6:
		$month_name = "Juni";
		break;

	case 7:
		$month_name = "Juli";
		break;

	case 8:
		$month_name = "Agustus";
		break;

	case 9:
		$month_name = "September";
		break;

	case 10:
		$month_name = "Oktober";
		break;

	case 11:
		$month_name = "November";
		break;

	case 12:
		$month_name = "Desember";
		break;
		}

	$ret_date = "$temp[2] $bulan $temp[0]";
	return $ret_date;
	}

/**
 * fungsi untuk mengeluarkan data dalam bentuk array
 *
 * @param unknown_type $script
 * @param unknown_type $file
 * @param unknown_type $line
 * @return array
 */

function getArray($script, $file = '', $line = '')
	{
	$result = mysql_query($script);
	if (!$result) errorSQL($script, $file, $line);
	$arr = array();
	for ($i = 0; $i < mysql_num_rows($result); $i++)
	for ($j = 0; $j < mysql_num_fields($result); $j++) $arr[$i][mysql_field_name($result, $j) ] = mysql_result($result, $i, mysql_field_name($result, $j));
	return $arr;
	}

function uuid()
	{

	// The field names refer to RFC 4122 section 4.1.2

	return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x', mt_rand(0, 65535) , mt_rand(0, 65535) , // 32 bits for "time_low"
	mt_rand(0, 65535) , // 16 bits for "time_mid"
	mt_rand(0, 4095) , // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
	bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)) , '01', 6, 2)) ,

	// 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
	// (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
	// 8 bits for "clk_seq_low"

	mt_rand(0, 65535) , mt_rand(0, 65535) , mt_rand(0, 65535) // 48 bits for "node"
	);
	}

function js_redirect($url)
	{
?>
    <script language=\”JavaScript\”>
    <!– hide code from displaying on browsers with JS turned off
      function redirect() {
        window.location = " . <?php echo $url; ?>. "
      }–>
    </script>
<?php
	return true;
	}

function toDuration($seconds)
	{
	$sec = 0;
	$min = 0;
	$hour = 0;
	$sec = $seconds % 60;
	$seconds = floor($seconds / 60);
	if ($seconds)
		{
		$min = $seconds % 60;
		$hour = floor($seconds / 60);
		}

	if ($seconds == 0 && $sec == 0) return sprintf("");
	  else return sprintf("%02d:%02d:%02d", $hour, $min, $sec);
	}

function formatSize($val)
	{
	$type = array(
		'B', //0  byte
		'kB', //1  kilo
		'MB', //2  mega
		'GB', //3  giga
		'TB', //4  tera
		'PB', //5  peta
		'EB', //6  exa
		'ZB', //7  zetta
		'YB'
	); //8  yotta
	$base = 1024;
	$step = 0;
	while ($val > $base)
		{
		$step++;
		$val = $val / $base;
		}

	return number_format($val, 2, ',', '.') . " " . $type[$step];
	}

function dropdown($name, array $options, $keys = null, $multiple = NULL, $selected = null, $flaging = null)
	{
	/*** begin the select ***/
	$dropdown = '<select name="' . $name . '" id="' . $name . '" fl="' . $flaging . '"  ' . ($multiple ? 'multiple="multiple"' : '');
	$dropdown.= 'style="width:100; ' . ($multiple ? 'height:100' : '') . '" >' . "\n";
	$dropdown.= '<option value="">--</option>';
	$selected = $selected;
	/*** loop over the options ***/
	foreach($options as $key => $option)
		{
		/*** assign a selected value ***/
		$select = $selected == $option[0] ? ' selected' : null;
		/*** add each option to the dropdown ***/
		$dropdown.= '<option  value="' . $option[0] . '" ' . $select . ' >' . ($keys ? $option[0] : '') . " " . $option[1] . '</option>' . "\n";
		}

	/*** close the select ***/
	$dropdown.= '</select>' . "\n";
	/*** and return the completed dropdown ***/
	return $dropdown;
	}

function dropdownGroup($name, $values, $selected = NULL, $attributes = array())
	{
	$attr_html = '';
	if (is_array($attributes) && !empty($attributes))
		{
		foreach($attributes as $k => $v)
			{
			$attr_html.= ' ' . $k . '="' . $v . '"';
			}
		}

	$output = '<select name="' . $name . '" id="' . $name . '"' . $attr_html . '>' . "\n";
	if (is_array($values) && !empty($values))
		{
		foreach($values as $key => $value)
			{
			if (is_array($value))
				{
				$output.= '<optgroup label="' . $key . '">' . "\n";
				foreach($value as $k => $v)
					{
					$sel = $selected == $k ? ' selected="selected"' : '';
					$output.= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>' . "\n";
					}

				$output.= '</optgroup>' . "\n";
				}
			  else
				{
				$sel = $selected == $key ? ' selected="selected"' : '';
				$output.= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>' . "\n";
				}
			}
		}

	$output.= "</select>\n";
	return $output;
	}

function getRealIpAddr()
	{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
	  else
		{
		$ip = $_SERVER['REMOTE_ADDR'];
		}

	return $ip;
	}

function wlog($s, $param)
	{
	/*
	$param = 1 : log Upload;
	$param = 2 : log Debug Query;
	*/
	if ($param == 1) $files = "upload";
	  else $files = "debug";
	$File = "../message/" . $files . "_" . date('Ymd') . ".log";
	$Handle = fopen($File, 'a');
	$ti = gmdate("D, d M Y H:i:s");
	$s = $ti . "\r\n" . $s . "\r\n";
	fwrite($Handle, $s);
	fclose($Handle);
	}

$msg_path = "/var/www/html/bni/message/";

function searchConcate($val)
	{
	$col = array(
		"cust_name" => "NAME_ON_CARD",
		"cust_phone" => "HOME_PHONE_BYTSR",
		"cust_status" => "LAST_RESPONSE_STATUS",
		"data_quota" => "QUOTA",
		"data_owned" => "DATA_OWNED",
		"data_vip" => "VIP_DATA",
		"cust_num" => "ID"
	);
	$fld = $col[$val];
	return $fld;
	}

function searchConcateFitur($val)
	{
	$col = array(
		"cust_name" => "NAME_ON_KTP",
		"cust_phone" => "HOME_PHONE_BYTSR",
		"cust_status" => "LAST_RESPONSE_STATUS",
		"data_quota" => "QUOTA",
		"data_owned" => "DATA_OWNED",
		"data_vip" => "VIP_DATA",
		"cust_num" => "CUST_NUM"
	);
	$fld = $col[$val];
	return $fld;
	}

// $x = filesize("tata.xtxt")

function datediff($d1, $d2)
	{
	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
	$d2 = (is_string($d2) ? strtotime($d2) : $d2);
	$diff_secs = abs($d1 - $d2);
	$base_year = min(date("Y", $d1) , date("Y", $d2));
	$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
	return array(
		"years" => date("Y", $diff) - $base_year,
		"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
		"months" => date("n", $diff) - 1,
		"days_total" => floor($diff_secs / (3600 * 24)) ,
		"days" => date("j", $diff) - 1,
		"hours_total" => floor($diff_secs / 3600) ,
		"hours" => date("G", $diff) ,
		"minutes_total" => floor($diff_secs / 60) ,
		"minutes" => (int)date("i", $diff) ,
		"seconds_total" => $diff_secs,
		"seconds" => (int)date("s", $diff)
	);
	}

function formatRupiah($val)
	{
	return number_format($val, 0, ',', '.');
	}

function formatDateEng($dDate)
	{
	$dNewDate = strtotime($dDate);
	if ($dDate) return date('Y-m-d', $dNewDate);
	}

function formatDateId($dDate)
	{
	$dNewDate = strtotime($dDate);
	if ($dDate) return date('d-m-Y', $dNewDate);
	}

/** masking text for phone number **/

function masking_text($maskTek = "", $type = "")
	{
	if ($type == '') $type = 'x';
	$ft = strlen($maskTek) - 6;
	$str.= substr($maskTek, 0, $ft);
	$fv = strlen($str) - 3;
	for ($i = $ft + 1; $i < strlen($maskTek) - 2; $i++)
		{
		$str.= $type;
		}

	return $str . substr($maskTek, -3, strlen($maskTek));
	}

?>
