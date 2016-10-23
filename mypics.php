<?php
session_start();
if (!isset($_SESSION['user']))
{
    header("Location: home.php");
    exit;
}
require_once 'config/CurlClient.class.php';
$curl = new CurlClient(API_URL."?action=general&general=images&user_id=".$_SESSION['user']->user_id);
$curl->post = 0;
$response = json_decode($curl->executePost());
if ($curl->getHttpCode() == 200)
{
    $images = $response->content;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Camagru</title>
    <link href="style/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="style/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Camagru</a>
        </div>

        <!-- Collect the nav links, "forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="login.php">Login<span class="sr-only">(current)</span></a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="home.php">Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-9">
            <div class="row">
                <h2>Your Pictures</h2>
                <?php
                if (isset($images))
                {
                    foreach ($images as $key => $value)
                    {?>
                        <div class="col-lg-2 col-md-2 col-xs-6 thumb">
                            <a class="thumbnail" href="#">
                                <img onclick="img_more(this, <?=$value->upload_id;?>);" class="img-responsive" src="<?=$value->file_name;?>" alt="user_img">
                            </a>
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <h4>Comments</h4>
            <div class="row">
                <img style="max-width: 300px;" src="https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif" alt="img1" id="frame" >
                <span style="display: none" id="frame_id"></span>
                <form style="display: none;" id="vis_form" class="form-signin" onsubmit="return false;">
                    <label for="visibility" class="sr-only">Privacy</label>
                    Show Publicly<input type="checkbox" id="visibility" onchange="toggle_visibility()" class="form-control" required>
                    <div class="text-center">
                        <img id="pre_loader" class="text-center" style="display: none;" src="style/images/facebook.gif" >
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="row"><div id="message_container" style="display: none;" class="text-center alert alert-success" role="alert">
                        <span id="message" ></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="well well-sm">This is a comment...</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="style/js/script.js"></script>
</body>
</html>