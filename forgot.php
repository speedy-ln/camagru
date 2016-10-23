<?php
session_start();
if (isset($_SESSION['user']))
{
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Camagru - Confirm Email</title>
    <link href="style/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="style/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Camagru</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="login.php">Login<span class="sr-only">(current)</span></a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Logout</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <div class="row">
        <form class="form-signin" onsubmit="return false;">
            <h2 class="form-signin-heading">Reset Email</h2>
            <label for="email" class="sr-only">Forgot Email</label>
            <input type="email" id="email" class="form-control" placeholder="Email" required autofocus>
            <button onclick="forgot_p();" id="confirm_btn" class="btn btn-lg btn-primary btn-block" >Forgot</button>
            <div class="text-center">
                <img id="pre_loader" class="text-center" style="display: none;" src="style/images/facebook.gif" >
            </div>
        </form>
        <div class="text-center" >
            <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3">
                <div id="message_container" style="display: none;" class="text-center alert alert-success" role="alert">
                    <p id="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Copyright lnkadime &copy 2016. </p>
    </div>
</footer>
<script type="text/javascript" src="style/js/script.js"></script>
</body>
</html>