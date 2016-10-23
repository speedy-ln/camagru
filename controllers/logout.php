<?php
/**
 * Created by IntelliJ IDEA.
 * User: Lebelo Nkadimeng
 * Date: 2016/10/22
 * Time: 7:57 PM
 */
session_start();
session_destroy();
header("Location: ../login.php");
exit;