<?php

session_start();

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

$data_type = "";
if (isset($_POST["data_type"])) {
    $data_type = $_POST['data_type'];
}

$destination = "";

if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {

    $allowed[] = "image/jpeg";
    $allowed[] = "image/png";

    $_FILES['file']['type'];

    if ($_FILES['file']['error'] == 0 && in_array($_FILES['file']['type'],$allowed)) {
        
        //good to go
        $folder = "uploads/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $destination = $folder . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $destination);

        $info->message = "Your image was uploaded";
        $info->data_type = $data_type;
        echo json_encode($info);

    }
}


if ($data_type == "change_profile_image") {
    if ($destination != '') {
        //save to database
        $id = $_SESSION['userid'];
        $query = "update users set image = '$destination' where userid = '$id' limit 1";
        $DB->write($query, []);
    }
} else if ($data_type == "send_image") {

    $arr['userid'] = "null";

    if (isset($_POST['userid'])) {
        $arr['userid'] = addslashes($_POST['userid']);
    }
    $arr['message'] = "";
    $arr['date'] = date("Y-m-d H:i:s");
    $arr['sender'] = $_SESSION['userid'];
    $arr['msgid'] = get_random_string_max(60);
    $arr['file'] = $destination;

    $arr2 = [
        'sender' => $_SESSION['userid'],
        'receiver' => $arr['userid']
    ];

    $sql = "SELECT * FROM messages WHERE (sender = :sender AND receiver = :receiver) OR (receiver = :sender AND sender = :receiver) LIMIT 1";
    $result2 = $DB->read($sql, $arr2);

    if (is_array($result2)) {
        $arr['msgid'] = $result2[0]->msgid;
    }

    $query = "INSERT INTO messages (sender, receiver, message, date, msgid, files) VALUES (:sender, :userid, :message, :date, :msgid, :file)";
    $DB->write($query, $arr);
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

/*Array{
   [file] => Array{
          [name] => IMG_20231114_133627 1.jpg
          [full_path] => IMG_20231114_133627 1.jpg
          [type] => image/jpeg
          [tmp_name] => F:\xamp\tmp\phpD599.tmp
          [error] => 0
          [size] => 160004
    }     
}
    
Array{
    [data_type] => cahnge_profile_image
}

*/