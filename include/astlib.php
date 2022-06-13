<?

//== Asterisk Manager Setting
//define("ASTMAN_HOST", "192.168.8.22");
define("ASTMAN_PORT", 5038);
define("ASTMAN_USER", "astcon");
define("ASTMAN_PASS", "astcon01");

//== ExtenSpy Command Setting
/**
ref: http://www.voip-info.org/wiki/view/Asterisk+cmd+ExtenSpy

b				: Only listens to channels which belong to a bridged call. 
g(grp)	: Only listens to channels where the channel variable ${SPYGROUP} is set to grp. ${SPYGROUP} can contain a : separated list of values. 
q				: Do not play a tone or say the channel name when listening starts on a channel. 
r(name)	: Records the listening session to the spool directory. A filename may be specified if desired; chanspy is the default. 
v(value): Sets the initial volume. The value may be between -4 and 4. 
w				: Enables "whisper" mode. Lets the spying channel talk to the spyed-on channel. 
W				: Enables "private whisper mode". The "spying" channel can whisper to the spyed-on channel, but cannot listen. 
h       : Hangup channel after spying done on target channel. (Sudah tidak ada lagi)
 */
//define("EXTENSPY_OPTION", "bqh");
//define("EXTENSPY_OPTION", "bqh");
define("EXTENSPY_OPTION", "bq");
//define("EXTENSPY_OPTION", "bqh"); //option 'h' sudah tidak ada lagi

function logDebug($msg){
  $logfile = '/tmp/astlib-'.date('Ymd').'.log';
  
  echo $logfile;
  
  $timestamp = date('Ymd His');
  $handle = fopen($logfile, "a");
  fwrite($handle, $timestamp.': '.$msg);
  fclose($handle);
}


/**
 Send message to asterisk
 return reply as array
 */
function mgrSendMsg($fp, $msg){	
	
	if(!$fp)return NULL;
  fwrite($fp, $msg, strlen($msg));
  if (function_exists('stream_set_timeout'))
  	stream_set_timeout ( $fp, 5);
  else
  	socket_set_timeout( $fp, 5);
  	
  logDebug("\n".$msg);
  
	/* read reply content */
	$msgReply = array();
	do{
		$line = fgets ( $fp);
		$msgReply[] = $line;
	}while ($line != "\r\n");  
  
	return $msgReply;
}

function mgrDisconnect($fp){
	fclose($fp);
}

               
function mgrConnect($mgrHost, $mgrPort, $mgrUser, $mgrPasswd){
	echo "mgrhost :{$mgrHost} |mgrport :{$mgrPort} |mgUser :{$mgrUser}| mgrPass :{$mgrPasswd} \r\n";
   $fp = fsockopen($mgrHost, $mgrPort, $errno, $errstr, 10);
   echo "mgrhost :{$mgrHost} |mgrport :{$mgrPort} |Err :{$errno}| Err str :{$errstr}";
   var_dump($fp);
  if(!$fp) {
  	echo "Server connection error to $mgrHost:$mgrPort";
    return NULL;
  }
  
  $loginMessage = "Action: login\r\n".
               		"Username: $mgrUser\r\n".
               		"Secret: $mgrPasswd\r\n".
               		"Events: off\r\n\r\n";
	
	$reply = mgrSendMsg($fp, $loginMessage);
	$n = count($reply);
	
	if($n < 3){
		mgrDisconnect($fp);
		return NULL;
	}
	
	if(!strncasecmp($reply[1], "Response:", 9)){
		$response = trim(substr($reply[1], 9));
		if(strcasecmp($response, "Success")){
			//authentication failed
			mgrDisconnect($fp);
			return NULL;
		}
	}
  
  return $fp;
}

	


function mgrWaitMsg($fp){
	/* read reply content */
	$msgReply = "";
	do{
		$line = fgets ( $fp);
		$msgReply .= $line;
	}while ($line != "\r\n");  
  
	return $msgReply;
}               	



function astShowChannels(){
	$fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);
	
	//get channel list
	$msg = "Action: Command\r\n".
				 "Command: core show channels concise\r\n\r\n";
	$reply = mgrSendMsg($fp, $msg);	
	
	$result = array();
	$cnt = count($reply);
	//don't include first 2 lines and last 2 lines
	for($i=2; $i<($cnt-2); $i++){
		if(!preg_match('_Outgoing Line_i',$reply[$i],$match)){
			$result[] = $reply[$i];
		}
	}
	
	//get channel count
	$msg = "Action: Command\r\n".
				 "Command: core show channels count\r\n\r\n";
	$reply = mgrSendMsg($fp, $msg);		
	
	$cnt = count($reply);
	//don't include first 2 lines and last 2 lines
	for($i=2; $i<($cnt-2); $i++){
		$result[] = $reply[$i];
	}
	mgrDisconnect($fp);
	return $result;
}

function astShowChannel($channel){
	$fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);
	
	$msg = "Action: Command\r\n".
				 "Command: core show channel $channel\r\n\r\n";
	$reply = mgrSendMsg($fp, $msg);	
	
	$result = array();
	$cnt = count($reply);
	//don't include first 2 lines and last 2 lines
	for($i=2; $i<($cnt-2); $i++){
		$result[] = $reply[$i];
	}
	mgrDisconnect($fp);
	return $result;
}

function astExtenSpy_original($fromext, $channel){
	$fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);
	
	$option = EXTENSPY_OPTION;
	if($option)
		$option = ",".$option;
	$msg = "Action: Originate\r\n".
				 "Channel: $fromext\r\n".	
				 "Application: ExtenSpy\r\n".
				 "Data: $channel".$option."\r\n".
				 "CallerID: B2B\r\n".
				 "\r\n";				 
	
	//echo nl2br($msg);
	$reply = mgrSendMsg($fp, $msg);	
	
	//var_dump($reply);
	mgrDisconnect($fp);
}

function astExtenSpy($fromext, $channel, $callerid, $opt){
	$fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);
	
	$option = EXTENSPY_OPTION;
	if($option){
		$option = ",".$option;
  }
  
  if($opt == "r"){
		$option = ",q".$opt.'('.$callerid.')';
  }
	$msg = "Action: Originate\r\n".
				 "Channel: $fromext\r\n".	
				 "Application: ExtenSpy\r\n".
				 "Data: $channel".$option."\r\n".
				 "CallerID: $callerid\r\n".
				 "Context: centerback\r\n".
				 "\r\n";				 
	$reply = mgrSendMsg($fp, $msg);	
	
	//var_dump($reply);
	mgrDisconnect($fp);
}

function astChanSpy($fromext, $channel,$callerid,$opt){
	$fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);
	$option = EXTENSPY_OPTION;
	if($option){
//		$option = ",".$option;
		$option = "|".$option;
  }
  if($opt == "r"){
		$option = ",q".$opt.'('.$callerid.')';
  }
	$msg = "Action: Originate\r\n".
				 "Channel: $fromext\r\n".	
				 "Application: ChanSpy\r\n".
				 "Data: $channel".$option."\r\n".
				 "CallerID: $callerid\r\n".
				 "Context: centerback\r\n".
				 "\r\n";				 
	echo 
	//echo nl2br($msg);
	$reply = mgrSendMsg($fp, $msg);	
	
	//var_dump($reply);
	logDebug($reply);
	mgrDisconnect($fp);
}

	function showChannelsAsXML(){  	
  	$output = '<?xml version="1.0" encoding="ISO-8859-1"?>';
  	
  	$lines = astShowChannels();
  	$output .= "<Channels>";
  	if(count($lines) == 0){
  		$output .= "<Item><ChannelData>Empty Channels</ChannelData></Item>";
  	}else{  	
		  foreach ($lines as $line){
		  	$chan = explode("!", $line);
		  	if(count($chan) > 12){		  		
		  		$output .= "<Item><ChannelData>";
		  		$output .= "<Channel>".$chan[0]."</Channel>".
  		               "<Context>".$chan[1]."</Context>".
  		               "<Callerid>".$chan[7]."</Callerid>".
  		               "<Extension>".$chan[2]."</Extension>";
					$output .= "</ChannelData></Item>";
		  	}
		  }
		}  
  	$output .= "</Channels>";
  	return $output;
  }
  
  
  function astChanSpyWhisper($fromext, $channel,$callerid,$opt){
        $fp = mgrConnect(ASTMAN_HOST, ASTMAN_PORT, ASTMAN_USER, ASTMAN_PASS);

        $option = EXTENSPY_OPTION."w";
        if($option){
                $option = "|".$option;
        }
        if($opt == "r"){
                $option = ",q".$opt.'('.$callerid.')';
        }
        $msg = "Action: Originate\r\n".
                                 "Channel: $fromext\r\n".
                                 "Application: ChanSpy\r\n".
                                 "Data: $channel".$option."\r\n".
                                 "CallerID: $callerid\r\n".
                                 "Context: centerback\r\n".
                                 "\r\n";

        //echo nl2br($msg);
        $reply = mgrSendMsg($fp, $msg);

        	mgrDisconnect($fp);
		}
?>
