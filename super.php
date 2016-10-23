<?php
session_start();
if (!isset($_SESSION['user']))
{
    header("Location: home.php");
    exit;
}
require_once 'config/CurlClient.class.php';
$curl = new CurlClient(API_URL."?action=general&general=imposable");
$curl->post = 0;
$response = json_decode($curl->executePost());
if ($curl->getHttpCode() == 200)
{
    $super_images = $response->content;
}
$curl->setApiUrl(API_URL."?action=general&general=images&user_id=".$_SESSION['user']->user_id);
$response = json_decode($curl->executePost());
if($curl->getHttpCode() == 200)
{
    $user_images = $response->content;
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
                <li><a href="home.php">Home</a></li>
                <li><a href="gallery.php">Snap</a></li>
                <li class="active"><a href="super.php">Make a Pic</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="controllers/logout.php">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
    <div class="row">
        <p class="well well-sm text-center">Select 1 picture from "Your Pictures" and 1 Picture from "Superimposable images".</p>
        <div class="col-sm-12 col-md-9">
            <div class="row">
                <h2>Your Pictures</h2>
                <?php
                if (isset($user_images))
                {
                    foreach ($user_images as $key => $value)
                    {?>
                        <div class="col-lg-2 col-md-2 col-xs-6 thumb">
                            <a class="thumbnail" href="#">
                                <img onclick="add_img(this, 1);" class="img-responsive" src="<?=$value->file_name;?>" alt="user_img">
                            </a>
                        </div>
              <?php }
                }
                ?>
            </div>
            <div class="row">
                <h1>Superimposable Image Gallery</h1>
                <?php
                if (isset($super_images))
                {
                    foreach($super_images as $key => $values)
                    {?>
                        <div class="col-lg-2 col-md-2 col-xs-6 thumb">
                            <a class="thumbnail" href="#">
                                <img onclick="add_img(this, 2);" class="img-responsive" src="<?=$values->file_name;?>" alt="super_img">
                            </a>
                        </div>
                    <?php }
                }
                ?>
            </div>
        </div>
        <div class="col-sm-12 col-md-3">
            <h4>Your selection</h4>
            <div class="row">
                <img style="max-width: 300px;" src="https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif" alt="img1" id="frame1" >
            </div>
            <div class="row">
                <img style="max-width: 300px;" src="https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif" alt="img2" id="frame2" >
            </div>
            <div class="row">
                <div class="row"><div id="message_container" style="display: none;" class="text-center alert alert-success" role="alert">
                        <span id="message" ></span>
                    </div></div>
                <br>
                <button disabled id="super" onclick="super_impose();" class="btn btn-success">Super Impose</button>
            </div>
            <div class="row">
                <img style="max-width: 150px;" src="https://dev.w3.org/2007/mobileok-ref/test/data/ROOT/GraphicsForSpacingTest/1/largeTransparent.gif" alt="img3" id="frame3" >
            </div>
        </div>
    </div>
</div>
<?php if(isset($_SESSION['user'])){ ?><div id="uid" style="display: none;"><?=$_SESSION['user']->user_id;?></div> <?php } ?>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Copyright lnkadime &copy 2016. </p>
    </div>
</footer>
<script type="text/javascript" src="style/js/script.js"></script>
</body>
</html>