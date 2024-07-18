<?php

session_start();

$DATA_RAW = file_get_contents("php://input");
$DATA_OBJ = json_decode($DATA_RAW);

$info = (object)[];

// Check if logged in
if (!isset($_SESSION["userid"])) {
    if (isset($DATA_OBJ->data_type) && $DATA_OBJ->data_type != "login" && $DATA_OBJ->data_type != "signup") {
        $info->logged_in = false;
        echo json_encode($info);
        die;
    }
}

require_once("classes/autoload.php");

$DB = new Database();

$arr['userid'] = $DATA_OBJ->find->userid ?? "null";

$sql = "SELECT * FROM users WHERE userid = :userid LIMIT 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    $arr['message'] = htmlspecialchars($DATA_OBJ->find->message, ENT_QUOTES, 'UTF-8');
    $arr['date'] = date("Y-m-d H:i:s");
    $arr['sender'] = $_SESSION['userid'];
    $arr['msgid'] = get_random_string_max(60);

    $arr2 = [
        'sender' => $_SESSION['userid'],
        'receiver' => $arr['userid']
    ];

    $sql = "SELECT * FROM messages WHERE (sender = :sender AND receiver = :receiver) OR (receiver = :sender AND sender = :receiver) LIMIT 1";
    $result2 = $DB->read($sql, $arr2);

    if (is_array($result2)) {
        $arr['msgid'] = $result2[0]->msgid;
    }

    $query = "INSERT INTO messages (sender, receiver, message, date, msgid) VALUES (:sender, :userid, :message, :date, :msgid)";
    $DB->write($query, $arr);

    // User found
    $row = $result[0];
    $row->image = get_user_image($row);

    $mydata = " Now Chatting With...<br>
              <div id='active_contact'>
                  <img src='{$row->image}'>
                  {$row->username}
              </div>";

    $messages = "
            <div id='messages_holder_parent' style='height:600px;'>
            <div id='messages_holder' style='height:550px;overflow-y:scroll;'>";

    // Read from DB
    $a['msgid'] = $arr['msgid'];

    $sql = "SELECT * FROM messages WHERE msgid = :msgid ORDER BY id DESC LIMIT 10";
    $result2 = $DB->read($sql, $a);

    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $myuser = $DB->get_user($data->sender);
            if ($_SESSION['userid'] == $data->sender) {
                $messages .= message_right($data, $myuser);
            } else {
                $messages .= message_left($data, $myuser);
            }
        }
    }

    $messages .= message_controls();

    $info->user = $mydata;
    $info->messages = $messages;
    $info->data_type = "send_message";
    echo json_encode($info);
} else {
    // User not found
    $info->message = "Select a contact";
    $info->data_type = "send_message";
    echo json_encode($info);
}

function get_random_string_max($length)
{
    $array = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $text = "";

    $length = rand(4, $length);
    for ($i = 0; $i < $length; $i++) {
        $random = rand(0, count($array) - 1);
        $text .= $array[$random];
    }

    return $text;
}

function get_user_image($row)
{
    $image = ($row->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
    if (file_exists($row->image)) {
        $image = $row->image;
    }
    return $image;
}

?>
