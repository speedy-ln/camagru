<?php

class Controller {
    

    /**
     * Standardized responses API returns.
     * @param $code int HTTP response code. Refer to REST class for codes.
     * @param $message string User friendly message.
     * @param mixed|string|array $content Additional content accompanying response to be used with backend.
     * @param bool|array|mixed $dbh
     * @return mixed
     */
    public function httpUserResponse($code, $message, $content = false, $dbh = false){
        if(REST::DEVELOPMENT){
            if($dbh !== false){
                if(isset($dbh->password))
                    unset($dbh->password);
                if(isset($dbh->dbh->password))
                    unset($dbh->dbh->password);
                if(isset($dbh->user_name))
                    unset($dbh->user_name);
                if(isset($dbh->dbh->user_name))
                    unset($dbh->dbh->user_name);
                if(isset($dbh->host_name))
                    unset($dbh->host_name);
                if(isset($dbh->dbh->host_name))
                    unset($dbh->dbh->host_name);
                if(isset($dbh->db_name))
                    unset($dbh->db_name);
                if(isset($dbh->dbh->db_name))
                    unset($dbh->dbh->db_name);
                $returnArray['errors'] = $dbh;
            }
        }
        $returnArray['content'] = $content;
        $returnArray['code'] = $code;
        $returnArray['message'] = $message;
        return  $returnArray;
    }

}
