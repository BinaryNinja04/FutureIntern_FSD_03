<?php

$sql = "select * from users where userid = :userid limit 1";
$id = $_SESSION['userid'];
$data = $DB->read($sql,['userid'=>$id]);

$mydata = "";

if(is_array($data)){
    $data = $data[0];

    $image = ($data->gender == "Male") ? "images/male.jpeg" : "images/female.jpeg";
    if(file_exists($data->image)){
        $image = $data->image;
    } 

    $gender_male = "";
    $gender_female = "";

    if($data->gender == "Male"){
        $gender_male = "checked";
    }else{
        $gender_female = "checked";
    }

$mydata = '

<style>
@keyframes appear{
    0%{opacity:0;transform:translateY(100px) rotate(10deg);transform-origin: 0% 0%}
    100%{opaxity:1;transform:translateY(0px) rotate(0deg);transform-origin: 0% 0%}
}

form{
    text-align:left;
    margin: 30px;
    padding: 10px;
    width: 100%;
    max-width: 400px;
}

input[type=text], [type=password], [type=button]{
    padding: 10px;
    margin: 10px;
    width: 200px;
    border-radius:5px;
}

input[type=button]{
    width: 224px;
    cursor: pointer;
    color: white;
    background-color: #244686;
}

input[type=radio]{
    transform: scale(1.2);
    cursor: pointer;
}

#error{
    font-size: 16px;
    text-align: center;
    padding: 0.5em;
    background-color: #ecaf91;
    color: white;
    display: none;
}

.dragging{
    border:dashed 2px #aaa;
}


</style>

        <div id="error">ERROR!!</div>
        <div style="display:flex;animation: appear 1s ease">
            <div>
            <span style="font-size:11px;">drag and drop an image to change</span><br>
              <img ondragover="handle_drag_and_drop(event)" ondrop="handle_drag_and_drop(event)" ondragleave="handle_drag_and_drop(event)" src="'.$image.'" style="width:200px;height:200px;margin:15px">
              <label for="change_image_input" id="change_image_button" style="background-color:grey;display:inline-block;padding:0.5rem;border-radius:1rem;width:2.75rem;cursor:pointer;">
              Edit
              </label>
              <input type="file" onchange="upload_profile_image(this.files)" id="change_image_input" style="display:none;">
            </div>
           <form id="myForm">
               <input type="text" name="username" placeholder="User Name" value="'.$data->username.'"><br>
               <input type="text" name="email" placeholder="Email" value="'.$data->email.'"><br>
               <div style="padding: 10px;">
                  <br>Gender:<br>
                  <input type="radio" value="Male" name="gender" '.$gender_male.'>Male<br>
                  <input type="radio" value="Female" name="gender" '.$gender_female.'>Female<br>
               </div>
               <input type="password" name="password" placeholder="Password" value="'.$data->password.'"><br>
               <input type="password" name="password2" placeholder="Confirm Password" value="'.$data->password.'"><br>
               <input type="button" value="Save" id="save_settings_button" onclick="collect_data(event)"><br>
            </form>
        </div>

';

$info->message = $mydata;
$info->data_type = "contacts";
echo json_encode($info);

}else{

$info->message = "No contacts were found";
$info->data_type = "error";
echo json_encode($info);
}

?>