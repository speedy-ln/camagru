/**
 * Created by Lebelo Nkadimeng on 2016/10/17.
 */
var api_url = "http://localhost/~mashesha/camagru/api/index.php";
// var api_url = "http://localhost:8080/camagru/api/index.php";
if (document.getElementById('inputFile'))
    document.getElementById('inputFile').addEventListener("change",upload_image);

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

function load_post_custom(url, params, callback)
{
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
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(params);
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
        window.location.replace("http://localhost/~mashesha/camagru/controllers/login.php?json="+xhr.responseText);
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
    if(xhr.status == 200)
        document.getElementById("message_container").className = "text-center alert alert-success";
    else
        document.getElementById("message_container").className = "text-center alert alert-danger";
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "block";
    setTimeout(function () {
        if (xhr.status == 200)
            window.location.href = "login.php";
        document.getElementById('message_container').style.display = "none";
        document.getElementById('register_btn').style.display = "block";
    },6000);
}

function save_photo()
{
    var canvas = document.getElementsByTagName("canvas");
    canvas.getContext('2d').drawImage(video, 0, 0, 350, 350);
    var data = canvas.toDataURL('image/png');
    // var xhttp = new XMLHttpRequest();
    // xhttp.open("POST", api_url, true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // xhttp.send("img=" + data + "&type=" + '0');
}

function upload_image()
{
    var data = new FormData();
    data.append("action", "upload");
    data.append("upload", "new_picture");
    data.append("user_id", document.getElementById('uid').innerHTML);
    data.append("file", document.getElementById('inputFile').files[0]);
    load_post(api_url, data, upload_img_callback);
}

function upload_img_callback(xhr)
{
    var response = JSON.parse(xhr.responseText);
    console.log(xhr.responseText);
    if(xhr.status == 200)
        document.getElementById("message_container").className = "text-center alert alert-success";
    else
        document.getElementById("message_container").className = "text-center alert alert-danger";
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "inline-block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
    },5000);
    if (document.getElementById('frame3'))
    {
        var frame3 = document.getElementById('frame3');
        if (response.content.file_name)
            frame3.src = response.content.file_name;
    }
}


function add_img(img, frame)
{
    var frame1 = document.getElementById('frame1');
    var frame2 = document.getElementById('frame2');
    if (frame == 1)
        frame1.src = img.src;
    else if (frame == 2)
        frame2.src = img.src;
    if (frame1.src != "https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif"
        && frame2.src != "https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif")
        document.getElementById('super').disabled = 0;
}

function super_impose()
{
    var frame1 = document.getElementById('frame1'),
        frame2 = document.getElementById('frame2'),
        data = new FormData();
    data.append("action", "upload");
    data.append("upload", "merge");
    data.append("user_id", document.getElementById('uid').innerHTML);
    data.append("img1", frame1.src);
    data.append("img2", frame2.src);
    load_post(api_url, data, upload_img_callback);
}

function img_more(doc, upload_id, n)
{
    var frame = document.getElementById('frame'),
        span = document.getElementById('upload_id');
    document.getElementById('num_likes').innerHTML = n;
    frame.src = doc.src;
    span.innerHTML = upload_id;
    if (document.getElementById('vis_form'))
        document.getElementById('vis_form').style.display = "block";
    document.getElementById('likes').style.display = "block";
    document.getElementById('com').style.display = "block";
    document.getElementById('comments').innerHTML = "";
    load_get(api_url+"?action=general&general=comments&upload_id="+upload_id, get_comments_callback);
}

function toggle_visibility()
{
    
}

function like()
{
    var data = new FormData();
    data.append("action", "general");
    data.append("general", "like");
    data.append("user_id", document.getElementById('uid').innerHTML);
    data.append("upload_id", document.getElementById('upload_id').innerHTML);
    load_post(api_url, data, upload_img_callback);
}

function comment()
{
    var data = new FormData();
    data.append("action", "general");
    data.append("general", "comment");
    data.append("user_id", document.getElementById('uid').innerHTML);
    data.append("upload_id", document.getElementById('upload_id').innerHTML);
    data.append("comment", document.getElementById('comm').value);
    load_post(api_url, data, comment_callback);
}

function comment_callback(xhr)
{
    var response = JSON.parse(xhr.responseText);
    if(xhr.status == 200)
        document.getElementById("message_container").className = "text-center alert alert-success";
    else
        document.getElementById("message_container").className = "text-center alert alert-danger";
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "inline-block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
    },5000);
    var div = document.getElementById('comments');
    div.innerHTML += "<div class=\"well well-sm\">" + document.getElementById('comm').value + "</div>";
}

function get_comments_callback(xhr)
{
    var response = JSON.parse(xhr.responseText);
    if(xhr.status == 200)
    {
        document.getElementById("message_container").className = "text-center alert alert-success";
        var div = document.getElementById('comments');
        div.innerHTML = "";
        for(var i = 0; i < response.content.length; i += 1)
        {
            div.innerHTML += "<div class=\"well well-sm\">" + response.content[i].comment + "<br> <small>- "
                + response.content[i].first_name + " " + response.content[i].last_name  + "</small></div>";
        }
    }
    else
        document.getElementById("message_container").className = "text-center alert alert-danger";
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "inline-block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
    },5000);
}

function confirm_e()
{
    start_preloader();
    document.getElementById('confirm_btn').style.display = "none";
    var data = new FormData();
    data.append("confirm_email", document.getElementById('confirm_email').value);
    data.append("action", "user");
    data.append("user", "confirm_email");
    load_post(api_url, data, confirm_email_callback);
}

function confirm_email_callback(xhr)
{
    stop_preloader();
    var response = JSON.parse(xhr.responseText);
    if(xhr.status == 200){
        document.getElementById("message_container").className = "text-center alert alert-success";
    }
    else
    {
        document.getElementById("message_container").className = "text-center alert alert-danger";
    }
    document.getElementById('message').innerHTML = response.message;
    document.getElementById('message_container').style.display = "block";
    setTimeout(function () {
        document.getElementById('message_container').style.display = "none";
        document.getElementById('confirm_btn').style.display = "block";
        if (xhr.status == 200) window.location.href = "login.php";
    },5000);
}

function forgot_p()
{
    start_preloader();
    document.getElementById('confirm_btn').style.display = "none";
    var data = new FormData();
    data.append("email", document.getElementById('email').value);
    data.append("action", "user");
    data.append("user", "forgot_password");
    load_post(api_url, data, confirm_email_callback);
}

function validate_p()
{
    var p1 =  document.getElementById('inputPassword');
    var p2 =  document.getElementById('confirmPassword');
    
    if (p1.value == p2.value && p1.value != "")
        document.getElementById('confirm_btn').disabled = false;
    else
        document.getElementById('confirm_btn').disabled = true;
}

function reset_passw()
{
    start_preloader();
    document.getElementById('confirm_btn').style.display = "none";
    var data = new FormData();
    data.append("reset_p", document.getElementById('reset_p').value);
    data.append("password", document.getElementById('inputPassword').value);
    data.append("action", "user");
    data.append("user", "reset_p");
    load_post(api_url, data, confirm_email_callback);   
}

function del_img(upload_id)
{
    var data = new FormData();
    data.append("user_id", document.getElementById('uid').value);
    data.append("upload_id", upload_id);
    data.append("action", "general");
    data.append("general", "delete_img");
    load_post(api_url, data, upload_img_callback);
}