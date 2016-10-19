<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/19
 * Time: 5:17 PM
 */
class Uploads extends Model
{
    private $upload_id;
    private $user_id;
    private $file_name;
    private $visible;
    private $created;
    private $table_name = "uploads";

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
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * @param mixed $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
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
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    public function getVars()
    {
        $return = array();
        $return['upload_id'] = $this->getUploadId();
        $return['user_id'] = $this->getUserId();
        $return['file_name'] = $this->getFileName();
        $return['visible'] = $this->getVisible();
        $return['created'] = $this->getCreated();
        return $return;
    }

    public function setVars($data)
    {
        if (isset($data['upload_id'])) $this->setUploadId($data['upload_id']);
        if (isset($data['user_id'])) $this->setUserId($data['user_id']);
        if (isset($data['file_name'])) $this->setFileName($data['file_name']);
        if (isset($data['visible'])) $this->setVisible($data['visible']);
        if (isset($data['created'])) $this->setCreated($data['created']);
    }
}