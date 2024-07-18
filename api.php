<?php

session_start();

$DATA_RAW = file_get_contents("php://input");
$DATA_OBJ = json_decode($DATA_RAW);

$info = (object) [];

//check if logged in
if (!isset($_SESSION["userid"])) {
    if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup") {
        $info->logged_in = false;
        echo json_encode($info);
        die;
    }
}

require_once ("classes/autoload.php");

$DB = new Database();


$Error = "";

if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "signup") {
    //signup
    include ("includes/signup.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "user_info") {
    //user_info
    include ("includes/user_info.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "login") {
    //signup
    include ("includes/login.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "logout") {
    //logout
    include ("includes/logout.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "contacts") {
    //user_info
    include ("includes/contacts.php");
} else if (isset($DATA_OBJ->data_type) && ($DATA_OBJ->data_type == "chats" || $DATA_OBJ->data_type == "chats_refresh")) {
    //user_info
    include ("includes/chats.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "settings") {
    //user_info
    include ("includes/settings.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "save_settings") {
    //user_info
    include ("includes/save_settings.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "send_message") {
    //user_info
    include ("includes/send_message.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_message") {
    //user_info
    include ("includes/delete_message.php");
} else if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type == "delete_thread") {
    //user_info
    include ("includes/delete_thread.php");
}

function message_left($data, $result)
{
    $image = ($result->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }

    $a = "
    <div id='message_left'>
              <div></div>
                  <img id='prof_img' src='$image'>
                  <b>$result->username</b><br>
                  $data->message<br><br>";

    if ($data->files != "" && file_exists($data->file)) {

        $a .= "<img src='$data->files' style='width:100%;cursor:pointer;' onclick='image_show(event)'/><br>";
    }

    $a .= "<span style='font-size:11px;color:#999;'>" . date("jS M Y H:i:s a", strtotime($data->date)) . "</span>
                  <img id='trash' src='images/trash.png' onclick='delete_message(event)' msgid='$data->id'/>
              </div>";

    return $a;
}

function message_right($data, $result)
{

    $image = ($result->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }

    $a = "
    <div id='message_right'>

    <div>";

    if ($data->seen) {
        $a .= " <img src='images/tick.png' style=''/>";
    } else if ($data->received) {
        $a .= " <img src='images/tick_grey.png' style=''/>";
    }

    $a .= "</div>
         <img id='prof_img' src='$image' style='float:right;''>
         <b>$result->username</b><br>
         $data->message<br><br>";

    if ($data->files != "" && file_exists($data->file)) {

        $a .= "<img src='$data->files' style='width:100%;cursor:pointer;' onclick='image_show(event)'/><br>";
    }

    $a .= "<span style='font-size:11px;color:#999;'>" . date("jS M Y H:i:s a", strtotime($data->date)) . "</span>

         <img id='trash' src='images/trash.png' onclick='delete_message(event)' msgid='$data->id'/>
     </div>";

    return $a;
}

function message_controls()
{
    return "
    </div>
    <span onclick='delete_thread(event)' style='color:purple;cursor:pointer;'>Delete this thread</span>
            <div style='display:flex;width:100%;height:40px;'>
               <label for='message_file'><img src='icons/clip.png' style='opacity:0.8;width:30px;cursor:pointer;margin:5px;'></label>
               <input type='file' id='message_file' name='file' style='display:none' onchange='send_image(this.files)'>
               <input id='message_text' onkeyup='enter_pressed(event)' style='flex:6;margin:0px;border:solid thin #ccc;border-bottom:none;font-size:14px;padding:4px' type='text' placeHolder='Type your message'>
               <input style='flex:1;cursor:pointer;' type='button' value='send' onclick='send_message(event)'>
            </div>
            </div> ";
}