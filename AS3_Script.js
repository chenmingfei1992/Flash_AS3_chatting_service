

// SECTION 1 - Make the initial request to simply populate the chat window

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function requestEntries():void {
	output_txt.text = "Populating chat window...";
	var variables_re:URLVariables = new URLVariables();

	var varSend_re:URLRequest = new URLRequest("chat.php");
	varSend_re.method = URLRequestMethod.POST;
	varSend_re.data = variables_re;

	var varLoader_re:URLLoader = new URLLoader;
	varLoader_re.dataFormat = URLLoaderDataFormat.VARIABLES;
	varLoader_re.addEventListener(Event.COMPLETE, completeHandler_re);

	function completeHandler_re(event:Event):void{

		if (event.target.data.returnBody == "") {
			output_txt.text = "No data coming through";
		} else {
			stored_id_txt.text = "" + event.target.data.stored_id;
			output_txt.condenseWhite = true;
			output_txt.htmlText = "" + event.target.data.returnBody;
		}
	
	}
	variables_re.requester = "initial_request";
	varLoader_re.load(varSend_re);
}
requestEntries();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// SECTION 2 - Set up a timer connected to a server call, checking for new chats

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Timer for auto refreshing every 5 seconds
// You can change this number to suit your needs
var fiveSecs:Timer = new Timer(1000, 5); 
fiveSecs.addEventListener(TimerEvent.TIMER, onTick); 
fiveSecs.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete); 
fiveSecs.start();

function onTick(event:TimerEvent):void { 
    timer_txt.text = "" + event.target.currentCount; 
}

function onTimerComplete(event:TimerEvent):void{
	
    var variables_cc:URLVariables = new URLVariables();
    var varSend_cc:URLRequest = new URLRequest("chat.php");
    varSend_cc.method = URLRequestMethod.POST;
    varSend_cc.data = variables_cc;
    var varLoader_cc:URLLoader = new URLLoader;
    varLoader_cc.dataFormat = URLLoaderDataFormat.VARIABLES;
    varLoader_cc.addEventListener(Event.COMPLETE, completeHandler_cc);

    function completeHandler_cc(event:Event):void{
	    if (event.target.data.statusline == "is_new") {
              output_txt.condenseWhite = true;
		      output_txt.htmlText = "" + event.target.data.returnBody;
		      stored_id_txt.text = "" + event.target.data.stored_id;
			  status_txt.text = "" + event.target.data.statusline;
	    } 
    }
	// ready the last_refresh_time variable for sending to PHP
	variables_cc.requester = "chat_check";
	variables_cc.stored_id = stored_id_txt.text;
    varLoader_cc.load(varSend_cc);
	fiveSecs.reset();
	fiveSecs.start();
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//                    SECTION 3 - Parsing new chats to the PHP file

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// This section handles parsing when the user chats
//input_txt.restrict = "A-Za-z 0-9";
input_txt.restrict = "^&<>";
// hide the little processing movieclip
processing_mc.visible = false;
// Assign a variable name for our URLVariables object
var variables_se:URLVariables = new URLVariables();
//  Build the varSend variable that is the URLRequest object
var varSend_se:URLRequest = new URLRequest("chat.php");
varSend_se.method = URLRequestMethod.POST;
varSend_se.data = variables_se;
// Build the varLoader variable
var varLoader_se:URLLoader = new URLLoader;
varLoader_se.dataFormat = URLLoaderDataFormat.VARIABLES;
varLoader_se.addEventListener(Event.COMPLETE, completeHandler_se);

// Handler for PHP script completion and return
function completeHandler_se(event:Event):void{
    // remove processing movieclip(or make invisible)
    processing_mc.visible = false;
	// Load the response from the PHP file
	stored_id_txt.text = "" + event.target.data.stored_id;
	output_txt.condenseWhite = true;
	output_txt.htmlText = "" + event.target.data.returnBody;
}

// Add an event listener for the submit button and what function to run
submit_btn.addEventListener(MouseEvent.CLICK, ValidateAndSend);
// Validate form fields and send the variables when submit button is clicked
function ValidateAndSend(event:MouseEvent):void{
	
    // validate form fields
    if(!input_txt.length || !uname_txt.length) {
         // Please type your name and chat content error display goes here
	} else {
		
        processing_mc.visible = true;
		
		variables_se.requester = "new_chat";
   		variables_se.user_name = uname_txt.text;
   		variables_se.chat_body = input_txt.text;	

   		varLoader_se.load(varSend_se);
		input_txt.text = ""; // Empty the input field
		output_txt.text = "Waiting for server connection...";

	} // close else after form validation
} // Close ValidateAndSend function //////////////////////////////////////////////////////////////
