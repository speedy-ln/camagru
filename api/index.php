<?php
session_start();
header('Access-Control-Allow-Origin: *');
foreach (glob("libraries/*.php") as $filename){
    require_once $filename;
}
foreach (glob("controllers/*.php") as $filename){
    require_once $filename;
}
foreach (glob("models/*.php") as $filename) {
    require_once $filename;
}

class Api extends REST{


    public function __construct() {
//        require_once 'config/config.php';
        parent::__construct();

    }

    public function json($data) {
        if (is_array($data)) {
            return json_encode($data, 1);
        } else {
            return $data;
        }
    }

    public function initializeApi($func = false) {
        $error = array();
//        $api_key = strtolower(trim(str_replace("/", "", $_REQUEST['api_key'])));
//
//        if ($api_key !== API_KEY){//disabled
//            $error['error'] = "You are trying to access information from an invalid source.";
//            $this->response($this->json($error), 401);
//        }

        if(is_bool($func)){
            $func = strtolower(trim(str_replace("/", "", $_REQUEST['action'])).'_'.$this->get_request_method());
            if((int) method_exists($this, $this->$func())){
                $this->$func();
            } else {
                $error = array("Method does not exist ".$func);
                $this->response($this->json($error), 401);
            }
        } else {
            $error['error'] = "The requested action is not allowed: ".$func;
            $this->response($this->json($error), 401);
        }
    }

    public function user_post(){
        $user = new UsersController();
        switch($this->_request['user']){
            case "login":
                $result = $user->login($this->_request);
                break;
            case "register";
                $result = $user->register($this->_request);
                break;
            case "reset_p":
                $result = $user->reset_password($this->_request);
                break;
            case "forgot_password":
                $result = $user->forgotPassword($this->_request);
                break;
            case "confirm_email":
                $result = $user->confirm_email($this->_request);
                break;
        }
        if(isset($result)){
            $this->response($this->json($result), $result['code']);
        } else {
            $error = array("code" => 405, "Bad request, please try again.", "content" => false);
            $this->response($this->json($error), 501);
        }
    }

    public function user_get(){
        $user = new UsersController();
        switch($this->_request['user']){
            case "id":
                $result = $user->getUser($this->_request);
                break;
        }
        if(isset($result)){
            $this->response($this->json($result), $result['code']);
        } else {
            $error = array("code" => 405, "Bad request, please try again.", "content" => false);
            $this->response($this->json($error), 501);
        }
    }

    public function general_get(){
        $user = new GeneralController();
        switch($this->_request['general']){
            case "imposable":
                $result = $user->getImposable();
                break;
            case "images":
                $result = $user->getUserImages($this->_request);
                break;
            case "all_img":
                $result = $user->getAllImages();
                break;
            case "comments":
                $result = $user->getComments($this->_request);
                break;
        }
        if(isset($result)){
            $this->response($this->json($result), $result['code']);
        } else {
            $error = array("code" => 405, "Bad request, please try again.", "content" => false);
            $this->response($this->json($error), 501);
        }
    }

    public function general_post(){
        $user = new GeneralController();
        switch($this->_request['general']){
            case "like":
                $result = $user->like($this->_request);
                break;
            case "comment":
                $result = $user->comment($this->_request);
                break;
            case "delete_img":
                $result = $user->delete_img($this->_request);
                break;
        }
        if(isset($result)){
            $this->response($this->json($result), $result['code']);
        } else {
            $error = array("code" => 405, "Bad request, please try again.", "content" => false);
            $this->response($this->json($error), 501);
        }
    }
    
    public function upload_post()
    {
        $user = new UploadsControllers();
        switch($this->_request['upload']){
            case "new_picture":
                $result = $user->new_picture($this->_request);
                break;
            case "base64":
                $result = $user->save_base($this->_request);
                break;
            case "merge":
                $result = $user->merge($this->_request);
                break;
        }
        if(isset($result)){
            $this->response($this->json($result), $result['code']);
        } else {
            $error = array("code" => 405, "Bad request, please try again.", "content" => false);
            $this->response($this->json($error), 501);
        }
    }

}
//echo json_encode($_FILES);
$api = new Api();
$api->initializeApi();