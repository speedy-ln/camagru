<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 10:01 PM
 */
class Comments extends Model
{
    private $comment_id;
    private $comment;
    private $upload_id;
    private $created;
    private $user_id;
    private $table_name = "comments";

    /**
     * @return mixed
     */
    public function getCommentId()
    {
        return $this->comment_id;
    }

    /**
     * @param mixed $comment_id
     */
    public function setCommentId($comment_id)
    {
        $this->comment_id = $comment_id;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getUploadId()
    {
        return $this->upload_id;
    }

    /**
     * @param mixed $upload_id
     */
    public function setUploadId($upload_id)
    {
        $this->upload_id = $upload_id;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

}