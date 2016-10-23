<?php
/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 6:53 PM
 */
session_start();
if(isset($_GET['json']))
{
    $var = json_decode($_GET['json']);
    if (isset($var->content[0]))
    {
        $temp = $var->content[0];
        if (isset($temp->user_id) && isset($temp->first_name))
        {
            $_SESSION['user'] = $var->content[0];
            header("Location: ../home.php");
            exit;
        }
    }
}
header("Location: ../login.php");
exit;