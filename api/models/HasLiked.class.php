<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 10:00 PM
 */
class HasLiked extends Model
{
    private $user_upload_id;
    private $user_id;
    private $upload_id;
    private $table_name = "has_liked";

    /**
     * @return mixed
     */
    public function getUserUploadId()
    {
        return $this->user_upload_id;
    }

    /**
     * @param mixed $user_upload_id
     */
    public function setUserUploadId($user_upload_id)
    {
        $this->user_upload_id = $user_upload_id;
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
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }
    

}