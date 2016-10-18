/**
 * Created by Lebelo Nkadimeng on 2016/10/17.
 */
// var api_url = "http://localhost/~mashesha/camagru/api/index.php";
var api_url = "http://localhost:8080/camagru/api/index.php";

function load_get(url, callback) {
    var xhr;
    if(typeof XMLHttpRequest !== 'undefined') xhr = new XMLHttpRequest();
    else {
        var versions = ["MSXML2.XmlHttp.6.0",
            "MSXML2.XmlHttp.5.0",
            "MSXML2.XmlHttp.4.0",
            "MSXML2.XmlHttp.3.0",
            "MSXML2.XmlHttp.2.0",
            "Microsoft.XmlHttp"];
        for(var i = 0, len = versions.length; i < len; i++) {
            try {
                xhr = new ActiveXObject(versions[i]);
                break;
            }
            catch(e){}
        }
    }
    xhr.onreadystatechange = ensureReadiness;
    function ensureReadiness() {
        if(xhr.readyState < 4) {
            return;
        }
        if(xhr.readyState === 4) {
            callback(xhr);
        }
    }
    xhr.open('GET', url, true);
    xhr.send('');
}

function load_post(url, params, callback){
    var xhr;
    if(typeof XMLHttpRequest !== 'undefined') xhr = new XMLHttpRequest();
    else {
        var versions = ["MSXML2.XmlHttp.5.0",
            "MSXML2.XmlHttp.4.0",
            "MSXML2.XmlHttp.3.0",
            "MSXML2.XmlHttp.2.0",
            "Microsoft.XmlHttp"];

        for(var i = 0, len = versions.length; i < len; i++) {
            try {
                xhr = new ActiveXObject(versions[i]);
                break;
            }
            catch(e){}
        } // end for
    }
    xhr.onreadystatechange = ensureReadiness;
    function ensureReadiness() {
        if(xhr.readyState < 4) {
            return;
        }

        if (xhr.readyState === 4) {
            callback(xhr);
        }
    }
    xhr.open('POST', url, true);
    xhr.send(params);
}

function start_preloader()
{
    document.getElementById('pre_loader').style.display = "inline-block";
}

function stop_preloader()
{
    document.getElementById('pre_loader').style.display = "none";
}

function login()
{
    start_preloader();
    document.getElementById('sign_in_btn').style.display = "none";
    var email = document.getElementById('inputEmail').value,
        password = document.getElementById('inputPassword').value,
        data = new FormData();
    data.append("email", document.getElementById('inputEmail').value);
    data.append("password", document.getElementById('inputPassword').value);
    data.append("action", "user");
    data.append("user", "login");
    load_post(api_url, data, login_callback);
}

function login_callback(xhr)
{
    stop_preloader();
    var response = JSON.parse(xhr.responseText);
    if(xhr.status == 200){
        document.getElementById("message_container").className = "text-center alert alert-success";
        alert("Hooray!!!");
    }
    else
    {
        document.getElementById("message_container").className = "text-center alert alert-danger";
    }
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
        document.getElementById('sign_in_btn').style.display = "block";
    },5000);
}

function register()
{
    start_preloader();
    document.getElementById('register_btn').style.display = "none";
    var data = new FormData();
    data.append("email", document.getElementById('inputEmail').value);
    data.append("password", document.getElementById('inputPassword').value);
    data.append("first_name", document.getElementById('inputFirstName').value);
    data.append("last_name", document.getElementById('inputLastName').value);
    data.append("action", "user");
    data.append("user", "register");
    load_post(api_url, data, register_callback);
}

function register_callback(xhr)
{
    stop_preloader();
    var response = JSON.parse(xhr.responseText);
    if(xhr.status == 200){
        document.getElementById("message_container").className = "text-center alert alert-success";
        alert("Hooray!!!");
    }
    else
    {
        document.getElementById("message_container").className = "text-center alert alert-danger";
    }
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
        document.getElementById('register_btn').style.display = "block";
    },5000);
}

function save_photo()
{
    var canvas = document.getElementsByTagName("canvas");
    canvas.getContext('2d').drawImage(video, 0, 0, 350, 350);
    var data = canvas.toDataURL('image/png');
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", api_url, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("img=" + data + "&type=" + '0');
}