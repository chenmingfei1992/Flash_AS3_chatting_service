<?php

// Connect to the database first thing
require_once "connect_to_mysql.php";

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Section 1 - Corresponds with section 1 in our flash AS3 script - Initial chat body request

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($_POST['requester'] == "initial_request") {
   
    $status_line = "initial load";
    $body = "";
    $sql = mysql_query("SELECT * FROM chats ORDER BY date_time DESC"); 
    while($row = mysql_fetch_array($sql)) { 
	    $id = $row["id"];
	    $user_name = $row["user_name"];
	    $chat_body = $row["chat_body"];
	    $date_time = $row["date_time"];
	    $chat_body = stripslashes($chat_body);
        $chat_body = eregi_replace("&#39;", "'", $chat_body);
	
        $body .= '<b><font color="#006699">' . $user_name . ': </font></b> 
	    <font color="#999999" size="-2">' . $date_time . '</font>  
	    <font color="#000000"> ' . $chat_body . '</font>
        <br />';

    }
	$sql = mysql_query("SELECT id FROM chats ORDER BY id DESC LIMIT 1"); 
    while($row = mysql_fetch_array($sql)) { 
	    $stored_id = $row["id"];
    }
    echo "stored_id=$stored_id&statusline=$status_line&returnBody=$body";

}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Section 2 - Corresponds with section 2 in our flash AS3 script - check for new chats

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($_POST['requester'] == "chat_check") {
	
	$status_line = "not_new";
	$stored_id = $_POST['stored_id'];
    $sql = mysql_query("SELECT id FROM chats ORDER BY id DESC LIMIT 1"); 
    while($row = mysql_fetch_array($sql)) { 
	    $latest_id = $row["id"];
    }
	
    if ($latest_id > $stored_id) {
         
		 $status_line = "is_new";
 	     $body = "";
         $sql = mysql_query("SELECT * FROM chats ORDER BY date_time DESC"); 
         while($row = mysql_fetch_array($sql)) { 
              $id = $row["id"];
              $user_name = $row["user_name"];
              $chat_body = $row["chat_body"];
              $date_time = $row["date_time"];
              $chat_body = stripslashes($chat_body);
              $chat_body = eregi_replace("&#39;", "'", $chat_body);

              $body .= '<b><font color="#006699">' . $user_name . ': </font></b> 
              <font color="#999999" size="-2">' . $date_time . '</font>  
              <font color="#000000"> '.$chat_body.'</font>
              <br />';

          }
		  echo "stored_id=$latest_id&statusline=$status_line&returnBody=$body";
	} 
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Section 3 - Corresponds with section 3 in our flash AS3 script - parsing new chats that are submitted

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($_POST['requester'] == "new_chat") {

     $user_ip = $_SERVER['REMOTE_ADDR']; 
     $user_name = $_POST['user_name'];
     $chat_body = $_POST['chat_body'];

      // Cleanse user input of SQL injection attacks before going to database		
	 $chat_body = eregi_replace("'", "&#39;", $chat_body);
	 $chat_body = eregi_replace("`", "&#39;", $chat_body);			
     $chat_body = mysql_real_escape_string($chat_body);
	 
     // delete any chat posts off of the tail end ------------------
      /*   $sqldeleteComments = mysql_query("SELECT id FROM chats ORDER BY date_time ASC LIMIT 0,1"); 
        
        while($row = mysql_fetch_array($sqldeleteComments)){ 
                	$cb_id = $row["id"];
					$deleteComments = mysql_query("DELETE FROM chats WHERE id='$cb_id'"); 
        }   */
        // End delete any comments off of the tail end -------------	 
     // Add this chat to the chat table
     $sql = mysql_query("INSERT INTO chats (user_ip, user_name, chat_body, date_time) 
        VALUES('$user_ip','$user_name','$chat_body',now())")  
        or die (mysql_error());
     
	 $latest_id = mysql_insert_id();

     $body = "";
     $sql = mysql_query("SELECT * FROM chats ORDER BY date_time DESC"); 
     while($row = mysql_fetch_array($sql)) {
	     $id = $row["id"];
	     $user_name = $row["user_name"];
	     $chat_body = $row["chat_body"];
	     $date_time = $row["date_time"];
	     $chat_body = stripslashes($chat_body);
         $chat_body = eregi_replace("&#39;", "'", $chat_body);
	
	     $body .= '<b><font color="#006699">' . $user_name . ': </font></b>
	     <font color="#999999" size="-2">' . $date_time . '</font>  
	     <font color="#000000"> '.$chat_body.'</font>
	     <br />';

     }
     
     echo "stored_id=$latest_id&statusline=poopoo&returnBody=$body";

} // close first if for post
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// close the mysql connection we made at the top of our script
mysql_close();
?>