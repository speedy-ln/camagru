<?php

/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/16
 * Time: 6:04 PM
 */
class Users extends Model
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
    public function getPassword()
    {
        return $this->pass;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->pass = password_hash($password, PASSWORD_DEFAULT);
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

    public function setVars($data)
    {
        if (isset($data['user_id'])) $this->setUsersId($data['user_id']);
        if (isset($data['first_name'])) $this->setName($data['first_name']);
        if (isset($data['last_name'])) $this->setSurname($data['last_name']);
        if (isset($data['email'])) $this->setEmail($data['email']);
        if (isset($data['password'])) $this->setPassword($data['password']);
    }

    public function getVars()
    {
        $return = array();
        $return['user_id'] = $this->getUsersId();
        $return['first_name'] = $this->getName();
        $return['last_name'] = $this->getSurname();
        $return['email'] = $this->getEmail();
        $return['password'] = $this->getPassword();
        $return['reset_p'] = $this->getResetP();
        return $return;
    }
    
}