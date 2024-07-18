<?php

$arr['userid'] = "null";

if (isset($DATA_OBJ->find->userid)) {
    $arr['userid'] = $DATA_OBJ->find->userid;
}

// Read messages from DB
$arr['sender'] = $_SESSION['userid'];
$arr['receiver'] = $arr['userid'];

$sql = "SELECT * FROM messages WHERE (sender = :sender AND receiver = :receiver) OR (receiver = :sender AND sender = :receiver )";
$result = $DB->read($sql, $arr);

if (is_array($result)) {

    foreach ($result as $row)
    {  

    if ($_SESSION['userid'] == $row->sender) {

        $sql = "UPDATE messages SET deleted_sender = 1 WHERE id = '$row->id' LIMIT 1";
        $DB->write($sql);
    }
    if ($_SESSION['userid'] == $row->receiver) {

        $sql = "UPDATE messages SET deleted_receiver = 1 WHERE id = '$row->id' LIMIT 1";
        $DB->write($sql);
    }
}
}
?>