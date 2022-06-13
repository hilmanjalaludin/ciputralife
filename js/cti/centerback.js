  var CBTNALL		 			= 0xFFFF
  var CBTNREADY 			= 0x0001
  var CBTNAUX   			= 0x0002
  var CBTNACW       	= 0x0004
  var CBTNOUTBOUND  	= 0x0008
  var CBTNPREDICTIVE 	= 0x0010
  
  var CBTNDIAL				= 0x0100
  var CBTNHOLD				= 0x0200
  var CBTNHANGUP			= 0x0400
  var CBTNTRANSFER		= 0x0800
  var CBTNCONFERENCE	= 0x1000
  
  //agent constant
  var AGENT_NULL				= 0  
  var AGENT_LOGIN				= 1
  var AGENT_READY				= 2  
  var AGENT_NOTREADY		= 3
  var AGENT_ACW					= 4  
  var AGENT_OUTBOUND		= 5
  var AGENT_PREDICTIVE	= 6
  var AGENT_BUSY				= 7
  
  //call constant  
	var CALLSTATUS_IDLE = 0;
	var CALLSTATUS_ALERTING = 1;
	var CALLSTATUS_CONNECTED = 2;
	var CALLSTATUS_SERVICEINITIATED = 3;
	var CALLSTATUS_ANSWERED = 4;
	var CALLSTATUS_HELD = 5;
	var CALLSTATUS_ORIGINATING = 6;
	var CALLSTATUS_TRUNKSEIZED = 7;
	
	//email constant
	var EMAIL_MEDIA			= 1;
  
  //global variable
	var onHold          = false;
	var onCall          = false;
	var connectedStatus = false;
	var agentStatus     = AGENT_NULL;
	var agentBtnState   = 0x0000;
	var callBtnState    = 0x0000;
	var callStatus      = 0;
	var warned          = 0;