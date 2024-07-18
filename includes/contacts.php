<?php

require_once("classes/autoload.php"); // Make sure to include the autoload if not already included

$myid = $_SESSION['userid'];
$sql = "SELECT * FROM users where userid != '$myid' LIMIT 10";
$myusers = $DB->read($sql);

$mydata = '
<style>
@keyframes appear{
    0%{opacity:0;transform:translateY(100px)}
    100%{opaxity:1;transform:translateY(0px)}
}

#contact{
    cursor:pointer;
    transition: all .5s cubic-bezier(.68,-2,.265,1.55);
}

#contact:hover{
    transform: scale(1.2);
}


</style>
<div style="text-align: center;anidmation: appear 1s ease">';

if (is_array($myusers)) {

    //check for new messages
    $msgs = array();
    $me = $_SESSION['userid'];
    $query = "SELECT * FROM messages WHERE receiver = $me AND received = 0";
    $mymgs = $DB->read($query,[]);

    if(is_array($mymgs)) {
        foreach ($mymgs as $row2) {
            $sender = $row->sender;
            if(isset($msgs[$sender])){
                $msgs[$sender]++;
            }else{
                $msgs[$sender] = 1;
            }
        }
    }    

    foreach ($myusers as $row) {

        $image = ($row->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
        if(file_exists($row->image)){
            $image = $row->image;
        }
        
        $mydata .= "
        <div id='contact' style='position:relative;' userid='$row->userid' onclick='start_chat(event)'>
            <img src='$image'>
            <br>$row->username";
           if(count($msgs) > 0 && isset($msgs[$row->userid])){
               $mydata .= " <div style='width:20px;height:20px;border-radius:50%;background-color:orange;color:white;position:absolute;left:0px;top:0px'>".$msgs[$row->userid]."</div>";
           }
            $mydata .= "
        </div>";
    }
    $mydata .= '</div>';
    
    $info->message = $mydata;
    $info->data_type = "contacts";
    echo json_encode($info);
} else {
    $info->message = "No contacts were found";
    $info->data_type = "error";
    echo json_encode($info);
}

die;
?>
