<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 9:57 PM
 */
class Likes extends Model
{
    private $like_id;
    private $likes;
    private $upload_id;
    private $table_name = "likes";

    /**
     * @return mixed
     */
    public function getLikeId()
    {
        return $this->like_id;
    }

    /**
     * @param mixed $like_id
     */
    public function setLikeId($like_id)
    {
        $this->like_id = $like_id;
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param mixed $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
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