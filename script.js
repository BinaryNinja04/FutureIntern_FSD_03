function _(element) {
    return document.getElementById(element);
}

var signup_button = _("signup_button");
signup_button.addEventListener("click", collect_data);


function collect_data() {

    signup_button.disabled = true;
    signup_button.value = "Loading....please wait..";
    var myForm = _("myForm");

    var inputs = myForm.getElementsByTagName("input");

    var data = {};
    for (var i = 0; i < inputs.length; i++) {
        var key = inputs[i].name;
        switch (key) {
            case "username":
                data.username = inputs[i].value;
                break;
            case "email":
                data.email = inputs[i].value;
                break;
               
            case "gender_male":    
            case "gender_female":
                if (inputs[i].checked) {
                    data.gender = inputs[i].value;
                }
                break;
            case "password":
                data.password = inputs[i].value;
                break;
            case "password2":
                data.password2 = inputs[i].value;
                break;
        }
    }
    send_data(data, "signup");

}

function send_data(data, type) {
    var xml = new XMLHttpRequest();
    xml.onload = function () {
        if (xml.readyState == 4 || xml.status == 200) {
            handle_result(xml.responseText);
            signup_button.disabled = false;
            signup_button.value = "Sign Up";
        }
    }
    data.data_type = type;
    var data_string = JSON.stringify(data);
    xml.open("POST", "api.php", true);
    xml.setRequestHeader("Content-Type", "application/json")
    xml.send(data_string);

}

function handle_result(result){
    var data = JSON.parse(result);
    if(data.data_type == "info"){
        window.location = "index.html";
    }else{
        var error = _("error");
        error.innerHTML = data.message;
        error.style.display = "block";
    }
}