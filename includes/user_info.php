<?php

$info = (object) [];

$data = []; // Initialize $data as an empty array

$data['userid'] = $_SESSION['userid'];

if ($Error == "") {

    $query = "select * from users where userid = :userid limit 1";
    $result = $DB->read($query, $data);

    if (is_array($result)) 
    {
        $result = $result[0];
        $result->data_type = "user_info";

        $image = ($result->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
        if(file_exists($result->image)){
            $image = $result->image;
        }
        $result->image = $image;
        echo json_encode($result);
    }
    else 
    {
        $info->message = "Wrong email !!";
        $info->data_type = "error";
        echo json_encode($info);
    }
} else {
    $info->message = $Error;
    $info->data_type = "error";
    echo json_encode($info);
}