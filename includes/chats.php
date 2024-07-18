<?php

$arr['userid'] = "null";

if (isset($DATA_OBJ->find->userid)) {
    $arr['userid'] = $DATA_OBJ->find->userid;
}

$refresh = false;
$seen = false;
if ($DATA_OBJ->data_type == "chats_refresh") {
    $refresh = true;
    $seen = $DATA_OBJ->find->seen;
    error_log("Chats refresh: seen = " . json_encode($seen)); // Debugging
}

$sql = "SELECT * FROM users WHERE userid = :userid LIMIT 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    // User found
    $result = $result[0];

    $image = ($result->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
    if (file_exists($result->image)) {
        $image = $result->image;
    }

    $result->image = $image;

    $mydata = "";

    if (!$refresh) {
        $mydata = "Now Chatting With...<br>
                   <div id='active_contact'>
                       <img src='$image'>
                       $result->username
                   </div>";
    }

    $messages = "";
    $new_message = false;

    if (!$refresh) {
        $messages = "
            <div id='messages_holder_parent' onclick='set_seen(event)' style='height:600px;'>
            <div id='messages_holder' style='height:550px; overflow-y:scroll;'>";
    }

    // Read messages from DB
    $a['sender'] = $_SESSION['userid'];
    $a['receiver'] = $arr['userid'];

    $sql = "SELECT * FROM messages WHERE (sender = :sender AND receiver = :receiver AND deleted_sender = 0) OR (receiver = :sender AND sender = :receiver AND deleted_receiver = 0) ORDER BY id DESC LIMIT 10";
    $result2 = $DB->read($sql, $a);

    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $myuser = $DB->get_user($data->sender);

            // Check for new messages
            if ($data->receiver == $_SESSION['userid'] && $data->received == 0) {
                $new_message = true;
            }

            if ($data->receiver == $_SESSION['userid'] && $seen) {
                $DB->write("UPDATE messages SET seen = 1 WHERE id = '$data->id' LIMIT 1");
                error_log("Message id {$data->id} marked as seen"); // Debugging
            }
            if ($data->receiver == $_SESSION['userid']) {
                $DB->write("UPDATE messages SET received = 1 WHERE id = '$data->id' LIMIT 1");
            }
            if ($_SESSION['userid'] == $data->sender) {
                $messages .= message_right($data, $myuser);
            } else {
                $messages .= message_left($data, $myuser);
            }
        }
    }

    if (!$refresh) {
        $messages .= message_controls();
    }

    $info->user = $mydata;
    $info->messages = $messages;
    $info->new_message = $new_message;
    $info->data_type = "chats";
    if ($refresh) {
        $info->data_type = "chats_refresh";
    }
    echo json_encode($info);

} else {
    // User not found, read recent messages
    $a['userid'] = $_SESSION['userid'];

    $sql = "SELECT * FROM messages WHERE (sender = :userid OR receiver = :userid) GROUP BY msgid ORDER BY id DESC LIMIT 10";
    $result2 = $DB->read($sql, $a);

    $mydata = "Preview chats...<br>";

    if (is_array($result2)) {
        $result2 = array_reverse($result2);
        foreach ($result2 as $data) {
            $other_user = $data->sender;
            if ($data->sender == $_SESSION['userid']) {
                $other_user = $data->receiver;
            }
            $myuser = $DB->get_user($other_user);

            $image = ($myuser->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
            if (file_exists($myuser->image)) {
                $image = $myuser->image;
            }

            $mydata .= "
                <div id='active_contact' userid='$myuser->userid' onclick='start_chat(event)' style='cursor:pointer;'>
                    <img src='$image'>
                    $myuser->username<br>
                    <span style='font-size:11px;'>$data->message</span>
                </div>";
        }
    }

    $info->user = $mydata;
    $info->messages = "";
    $info->data_type = "chats";

    echo json_encode($info);
}

?>
