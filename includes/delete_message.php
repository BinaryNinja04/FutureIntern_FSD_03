<?php

$arr['resultid'] = "null";

if (isset($DATA_OBJ->find->resultid)) {
    $arr['resultid'] = $DATA_OBJ->find->resultid;
}

$sql = "SELECT * FROM messages WHERE id = :resultid LIMIT 1";
$result = $DB->read($sql, $arr);

if (is_array($result)) {
    $result = $result[0];
    if ($_SESSION['userid'] == $result->sender) {

        $sql = "UPDATE messages SET deleted_sender = 1 WHERE id = '$result->id' LIMIT 1";
        $DB->write($sql);
    }
    if ($_SESSION['userid'] == $result->receiver) {

        $sql = "UPDATE messages SET deleted_receiver = 1 WHERE id = '$result->id' LIMIT 1";
        $DB->write($sql);
    }
}
?>