/*
 * If call busy identified starting time 
 * and if sstop by user or Customer stop
 * or end of time 
 * author: omens
 * created : 2013-09-12
 */ 

 
 /* 
  * handling definition 
  * by session login data 
  */
var USER_ROOT = 9;
var USER_ADMIN = 1;
var USER_MANAGER = 2;
var USER_SUPERVISOR = 3;
var USER_TELESALES = 4;
var USER_QUALITY = 4;

/* definition parameters **/
 
var DBConfig = '../class/class.save.counter.php';
var seconds = 0;
var intervalid;

var sessionKeys = function()
{
	return document.ctiapplet.getCallSessionKey();
 }
 
/* 
 *  save dat key sesion obejct data 
 */
 
 var CallSessionKeys = {
	 CallSessionId : '',
	 CallCustomerId : '',
	 CallCallerNumber: ''
 }

 
var start_timer_connected = function()
{
	stoped_timer_ringing();	
	clearInterval(intervalid);
	start_database('start_counter_connected');
	seconds = 0;
	intervalid = setInterval(function(){myTimer()},1000);
	document.getElementById('lebel_counter').innerHTML = " <span style='font-size:13px;color:#75aa67;'> Talking \"</span>";
	var myTimer = function()
	{
		seconds+=1;
		document.getElementById("time_counter").innerHTML = "<span style='font-size:13px;color:#75aa67;font-weight:bold;'>"+seconds_to_time(seconds)+"</span>";
	}
}
 
 
/*
 * Start timer run if status call BUSY  BY system 
 * handle by Call status CTI Global Function
 * please set before change.
 */
 
var start_timer_ringing = function()
{
	start_database('start_counter_ringing');
	seconds = 0;
	intervalid = setInterval(function(){myTimer()},1000);
	document.getElementById('lebel_counter').innerHTML = " <span style='font-size:13px;color:#96a1ea;'> Ringing \"</span>";
	var myTimer = function()
	{
		seconds+=1;
		document.getElementById("time_counter").innerHTML = "<span style='font-size:13px;color:#96a1ea;font-weight:bold;'>"+seconds_to_time(seconds)+"</span>";
	}
}

/*
 * Stop timer run if status call idle  by system 
 * handle by Call status CTI Global Function
 * please set before change.
 */

var stoped_timer_disconnect = function()
{
	clearInterval(intervalid);
	document.getElementById("time_counter").innerHTML ='-';
}

/*
 * start timer run if disconect and ACW Starting 
 * until save call Status 
 */
 
var start_timer_acw = function()
{
	stoped_timer_disconnect();
	start_database('start_counter_acw');
	seconds = 0;
	intervalid = setInterval(function(){myTimer()},1000);
	document.getElementById('lebel_counter').innerHTML = " <span style='font-size:13px;color:#7c7a7a;'>ACW \"</span>";
	var myTimer = function()
	{
		seconds+=1;
		document.getElementById("time_counter").innerHTML = "<span style='font-size:13px;color:#7c7a7a;font-weight:bold;'>"+seconds_to_time(seconds)+"</span>";
	}
}

/*
 * Stop timer run if ended ringing
 */

var stoped_timer_ringing = function()
{
	clearInterval(intervalid);
	document.getElementById("time_counter").innerHTML ='-';
}


/*
 * Converting time miliseconds to format duration time
 * handle by Call status CTI Global Function
 * please set before change.
 */

var seconds_to_time = function(totalSec)
{
	var hours 	= parseInt( totalSec / 3600 ) % 24;
	var minutes = parseInt( totalSec / 60 ) % 60;
	var seconds = totalSec % 60;
	var result  = (hours<10?"0"+hours:hours)+":"+(minutes<10?"0"+minutes:minutes)+":"+(seconds<10?"0"+seconds:seconds);
	return result;
}


/* start save data end get session data **/

var start_database = function(option)
{
	doJava.File = DBConfig;
		doJava.Params = {
			action		 : 'save_counter', 
			CounterType  : option, //'start_counter', 
			SessionKey	 : sessionKeys(),
			CustomerId   : phoneCall.CustomerId, 
			CallerNumber : phoneCall.initNumber
		}
	
	var error_data = doJava.eJson();
	if( error_data.result )
	{
		if( error_data.sessionCallId!='' )
		{
			CallSessionKeys.CallSessionId = error_data.sessionCallId;
			CallSessionKeys.CallCustomerId = phoneCall.CustomerId;
			CallSessionKeys.CallCallerNumber = phoneCall.initNumber;
		}	
	}
}


/* start save data end get session data **/

var stop_database = function(option)
{
	doJava.File = DBConfig;
	doJava.Params = 
	{
		action		 : 'save_counter', 
		CounterType  : option, 
		CustomerId   : CallSessionKeys.CallCustomerId,
		CallerNumber : CallSessionKeys.CallCallerNumber,
		SessionKey 	 : CallSessionKeys.CallSessionId
	}
	
	var error = doJava.eJson();
	if( error.result )
	{
		clearInterval(intervalid);
		setTimeout(function(){
			document.getElementById("time_counter").innerHTML ='-';
			document.getElementById("lebel_counter").innerHTML ='-';
		},1000);
	}	
}

/* 
 * main init control data *
 * main index of this function
 */
 
 var CounterFunction = function(USER_HANDLING, CALL_STATUS)
 {
	if((USER_HANDLING==USER_TELESALES) || (USER_HANDLING==USER_SUPERVISOR))
	{
		switch(CALL_STATUS)
		{
			case CALLSTATUS_IDLE: start_timer_acw(); break; // add start AWC && stop talking counter ( omens ) 
			case CALLSTATUS_ALERTING: start_timer_ringing();  break; // start_on_event_alerting; 
			case CALLSTATUS_CONNECTED: start_timer_connected(); break; // stop call talking next to Acw duration 
		}
	}
 }