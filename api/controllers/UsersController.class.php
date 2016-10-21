<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/16
 * Time: 6:18 PM
 */
class UsersController extends Controller
{

    public function register($_request)
    {
        if (!isset($_request['first_name']) || empty($_request['first_name'])
            || !isset($_request['last_name']) || empty($_request['last_name'])
            || !isset($_request['email']) || empty($_request['email'])
            || !isset($_request['password']) || empty($_request['password']))
        {
            $error_msg = "Please provide";
            if (!isset($_request['first_name']) || empty($_request['first_name']))
                $error_msg .= " name,";
            if (!isset($_request['last_name']) || empty($_request['last_name']))
                $error_msg .= " surname,";
            if (!isset($_request['email']) || empty($_request['email']))
                $error_msg .= " email address,";
            if (!isset($_request['password']) || empty($_request['password']))
                $error_msg .= " password,";
            $error_msg = rtrim($error_msg,",");
            $error_msg .= " in order to register.";
            return $this->httpUserResponse(422, $error_msg);
        }
        if ($this->userExists($_request['email']))
            return $this->httpUserResponse(400, 'This email address has already been registered. Please login.');
        $user = new Users();
        $user->setVars($_request);
        $insert = $user->appendArray($user->getVars());
        $registered = $user->insert($user->getTableName(), $insert);
        if ($registered === FALSE)
            return $this->httpUserResponse(422, "Unable to complete registration.", false, $user->dbh);
        return $this->httpUserResponse(200, "Registration successful.", false);
    }

    private function userExists($email){
        $user = new Users();
        $select = $user->select($user->getTableName(), array(), array('email' => $email));
        if(is_array($select) && count($select) > 0){
            return true;
        }
        return false;
    }

    public function login($_request)
    {
        if(!isset($_request['email'])){
            return $this->httpUserResponse(400, 'Please provide an email address in order to login.');
        }
        if(!isset($_request['password'])){
            return $this->httpUserResponse(400, 'Password field is compulsory.');
        }
        $users = new Users();
        $condition = array("email" => $_request['email']);
        $select_result = $users->select($users->getTableName(), array(), $condition);
        if(is_array($select_result) && count($select_result) > 0)
        {
            if(password_verify($_request['password'], $select_result[0]['password'])){
                if(isset($select_result[0]['password'])) unset($select_result[0]['password']);
                if (isset($select_result[0]['reset_p'])) unset($select_result[0]['reset_p']);
                return $this->httpUserResponse(200, "Welcome back.", $select_result);
            }
            return $this->httpUserResponse(422, "Email and password combination incorrect.", false, $users->dbh);
        }
        return $this->httpUserResponse(400, "Your email address is not registered, please register in order to login.", false, $users->dbh);
    }

    public function reset_password($_request)
    {
        if(!isset($_request['reset_p'])){
            return $this->httpUserResponse(401, 'Invalid token.');
        }
        if(!isset($_request['password']) || !isset($_request['confirm_password'])){
            return $this->httpUserResponse(400, 'Please specify a new password.');
        }
        if($_request['password'] != $_request['confirm_password']){
            return $this->httpUserResponse(422, 'Passwords don\'t match.');
        }
        $users = new Users();
        $condition = array("reset_p" => $_request['reset_p']);
        $select = $users->select($users->getTableName(), array("user_id"), $condition);
        if(is_array($select))
        {
            if(count($select) == 1)
            {
                $users->setPassword($_request['password']);
                $update = array("password" => $users->getPassword());
                $updated = $users->update($users->getTableName(), $update, array("user_id" => $select[0]['user_id']));
                if($updated)
                {
                    return $this->httpUserResponse(200, 'Your password has been updated.');
                } else {
                    return $this->httpUserResponse(422, 'We were not able to update your password at this time. Please try again later.');
                }
            } else {
                return $this->httpUserResponse(401, 'Token expired. Please email support or try again.');
            }
        }
        return $this->httpUserResponse(400, 'An error occurred. Please try again later.', false, $users->dbh);
    }

    public function forgotPassword($_request)
    {
        if(!isset($_request['email'])){
            return $this->httpUserResponse(400, 'Invalid email address.');
        }
        $user = new Users();
        $user_details = $user->select($user->getTableName(), array(), array("email" => $_request['email']));
        if(is_array($user_details) && count($user_details) > 0)
        {
            $guid = uniqid();
            $update = array("reset_p" => $guid);
            $updated = $user->update($user->getTableName(), $update, array("user_id" => $user_details[0]['user_id']));
            if ($updated)
            {
                $headers = "From: lnnkadimeng@gmail.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $msg = "Hello ".$user_details[0]['first_name']." \n\n Please click on the this link to reset your password <a href='http://localhost:8080/camagru/reset.php?rp=".$user_details[0]['reset_p']."'>http://localhost:8080/camagru/reset.php?rp=".$user_details[0]['reset_p'] ."</a>. If you didn't forget your password, please ignore this email. \n\n Regards \nCamagru";
                if(mail($_request['email'], "Password Reset", $msg, $headers))
                    return $this->httpUserResponse(200, 'An email has been sent to '.$_request['email'].'. Please check your email to reset your password.');
                else
                    return $this->httpUserResponse(422, 'Unable to send an email right now, please try again later.');
            }
            return $this->httpUserResponse(400, 'Unable to reset your password. Please contact support.');
        }
        return $this->httpUserResponse(400, 'It appears we don\'t have this email address: '.$_request['email'].' in our records. Please sign up.');
    }

    public function edit($_request)
    {
        $user = new Users();
        $user->setVars($_request);
        $update = $user->appendArray($user->getVars());
        $user->update($user->getTableName(), $update, array('user_id' => $_request['user_id']));

    }
}