<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 1:18 PM
 */
class GeneralController extends Controller
{
    public function getImposable()
    {
        $img = new ImgLib();
        $select_result = $img->select($img->getTableName());
        if(is_array($select_result))
            return $this->httpUserResponse(200, "Query successful.", $select_result);
        return $this->httpUserResponse(400, "An error occurred.", false, $img->dbh);
    }

    public function getUserImages($_request)
    {
        if (!isset($_request['user_id']))
            return $this->httpUserResponse(400, "Please login.");
        $uploads = new Uploads();
        $select = $uploads->select($uploads->getTableName(),array(), array("user_id" => $_request['user_id']));
        if(is_array($select))
            return $this->httpUserResponse(200, "Query successful.", $select);
        return $this->httpUserResponse(400, "An error occurred.", false, $uploads->dbh);
    }

    public function getAllImages()
    {
        $uploads = new Uploads();
//        $select = $uploads->select($uploads->getTableName(), array(), array("visible" => 1));
        $tables = array("likes", "uploads");
        $joins = array("RIGHT JOIN");
        $cond = array("likes.upload_id = uploads.upload_id");
        $columns = array("uploads.upload_id", "uploads.user_id", "uploads.file_name",
	                "uploads.visible", "uploads.created", "IFNULL(likes.likes, 0) AS likes", "IFNULL(likes.like_id, 0) AS like_id" );
        $select = $uploads->join($tables, $joins, $cond, array(), $columns);
        if(is_array($select))
            return $this->httpUserResponse(200, "Query successful.", $select);
        return $this->httpUserResponse(400, "An error occurred.", false, $uploads->dbh);
    }

    public function like($_request)
    {
        if (!isset($_request['user_id']))
            return $this->httpUserResponse(400, "Please login.");
        if (!isset($_request['upload_id']))
            return $this->httpUserResponse(400, "An error occurred.");
        $has_liked = new HasLiked();
        $insert = array('user_id' => $_request['user_id'], 'upload_id' => $_request['upload_id']);
        $select = $has_liked->select($has_liked->getTableName(), array(), $insert);
        if (is_array($select))
        {
            if (count($select) > 0)
                return $this->httpUserResponse(200, "You've already liked this post.");
            $liked = $this->like_image($_request['upload_id']);
            if ($liked) {
                $result = $has_liked->insert($has_liked->getTableName(), $insert);
                if ($result !== false)
                    return $this->httpUserResponse(200, "Liked.");
            }
            return $this->httpUserResponse(400, "Unable to like this post.");
        }
        return $this->httpUserResponse(400, "An error occurred.");
    }

    private function like_image($upload_id)
    {
        $likes = new Likes();
        $select = $likes->select($likes->getTableName(), array(), array('upload_id' => $upload_id));
        if (is_array($select) && count($select) > 0)
        {
            $insert = array('likes' => $select[0]['likes'] + 1);
            return ($likes->update($likes->getTableName(), $insert, $select[0]['like_id']));
        }
        $insert = array('likes' => 1, 'upload_id' => $upload_id);
        $res = $likes->insert($likes->getTableName(), $insert);
        if ($res !== false)
            return true;
        return false;
    }

    public function comment($_request)
    {
        if (!isset($_request['user_id']))
            return $this->httpUserResponse(400, "Please login.");
        if (!isset($_request['upload_id']) || !isset($_request['comment']))
            return $this->httpUserResponse(400, "An error occurred.");
        if (empty($_request['comment']))
            return $this->httpUserResponse(422, "Comment cannot be blank.");
        $comments = new Comments();
        $insert = array('comment' => $_request['comment'],
            'upload_id' => $_request['upload_id'],
            'user_id' => $_request['user_id']);
        $result = $comments->insert($comments->getTableName(), $insert);
        if ($result !== false)
        {
            $this->emailNewComment($_request['upload_id']);
            return $this->httpUserResponse(200, "Comment posted.");
        }
        return $this->httpUserResponse(400, "Unable to make a comment at this time. Please try again later.", false, $comments->dbh);
    }

    public function getComments($_request)
    {
        if (!isset($_request['upload_id']))
            return $this->httpUserResponse(400, "An error occurred.");
        $comments = new Comments();
        $tables = array("users", "comments");
        $join = array("INNER JOIN");
        $cond = array("users.user_id = comments.user_id");
        $where = array("comments.upload_id" => $_request['upload_id']);
        $columns = array("comments.`comment`", "users.first_name", "users.last_name");
//        $select = $comments->select($comments->getTableName(), array(), array('upload_id' => $_request['upload_id']));
        $select = $comments->join($tables, $join, $cond, $where, $columns);
        if (is_array($select))
            return $this->httpUserResponse(200, "Query Successful.", $select);
        return $this->httpUserResponse(400, "An error occurred.", $select, $comments->dbh);
    }

    private function emailNewComment($upload_id)
    {
        $upload = new Uploads();
        $select = $upload->select($upload->getTableName(), array(), array('upload_id'=> $upload_id));
        if (is_array($select) && count($select) > 0)
        {
            $users = new Users();
            $get_user = $users->select($users->getTableName(), array(), array('user_id' => $select[0]['user_id']));
            if (is_array($get_user) && count( $get_user) > 0)
            {
                $headers = "From: lnnkadimeng@gmail.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $msg = "Hello ".$get_user[0]['first_name']." \n\n One of your images has a new comment, please login to view the comment. \n\n Regards \nCamagru";
                mail($get_user[0]['email'], "New Comment", $msg, $headers);
            }
        }
    }

    public function delete_img($_request)
    {
        if (!isset($_request['upload_id']))
            return $this->httpUserResponse(400, "An error occurred.");
        $upload = new Uploads();
        $result = $upload->delete($upload->getTableName(), array('upload_id' => $_request['upload_id']));
        if ($result)
            return $this->httpUserResponse(200, "Deleted successfully.");
        return $this->httpUserResponse(400, "Unable to process query right now, please try again later.", false, $upload->dbh);
    }

}