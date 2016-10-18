<?php

/**
 * Created by PhpStorm.
 * User: lnkadime
 * Date: 2016/10/18
 * Time: 11:41 AM
 */
class example
{
    private $users_id;
    private $name;
    private $surname;
    private $email;
    private $pass;
    private $reset_p;
    private $table_name = "users";

    /**
     * @return mixed
     */
    public function getUsersId()
    {
        return $this->users_id;
    }

    /**
     * @param mixed $users_id
     */
    public function setUsersId($users_id)
    {
        $this->users_id = $users_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @return mixed
     */
    public function getResetP()
    {
        return $this->reset_p;
    }

    /**
     * @param mixed $reset_p
     */
    public function setResetP($reset_p)
    {
        $this->reset_p = $reset_p;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }


}