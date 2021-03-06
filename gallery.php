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
if($curl->getHttpCode() == 200)
    $user_images = $response->content;
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
                <?php if (!isset($_SESSION['user'])){?>
                <li><a href="login.php">Login<span class="sr-only">(current)</span></a></li>
                <li><a href="register.php">Register</a></li>
                <?php } ?>
                <?php if (isset($_SESSION['user'])){?>
                    <li><a href="home.php">Home</a></li>
                    <li class="active"><a href="#">Snap</a></li>
                    <li><a href="super.php">Make a Pic</a></li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($_SESSION['user'])){?>
                <li><a href="controllers/logout.php">Logout</a></li>
                <?php } ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container-fluid">
    <div class="row">
        <h1>Snap a Pic</h1>
        <div class="col-sm-12 col-md-9">
            <div class="col-md-6 col-sm-6">
                <div class="row"><video id="video"></video></div>
                <button class="btn btn-default" onclick="document.getElementById('save_photo').style.display='inline-block';" id="startbutton">Take Photo</button>
                <button class="btn btn-info" style="display: none;" id="save_photo" >Save Photo</button>
            </div>
            <div class="col-md-6 col-sm-6">
                <img src="http://placekitten.com/g/320/261" id="photo" alt="photo">
            </div>
            <br>
            <div class="row"><div id="message_container" style="display: none;" class="text-center alert alert-success" role="alert">
                <span id="message" ></span>
            </div></div>
            <br>
            <div class="col-lg-3 col-md-4 col-xs-6">
                <form class="form-signin" enctype="multipart/form-data" onsubmit="return false;">
                    <h2 class="form-signin-heading">Upload Image</h2>
                    <label for="inputFile" class="sr-only">Image</label>
                    <input type="file" name="file" id="inputFile" accept="image/jpeg,image/png" class="form-control" required >
                    <!--<button onclick="upload();" id="upload_btn" class="btn btn-lg btn-primary btn-block" >Upload</button>-->
                    <div class="text-center">
                        <img id="pre_loader" class="text-center" style="display: none;" src="style/images/facebook.gif" >
                    </div>
                </form>
            </div>
            <div class="row">
                <h1>Superimposable Image Gallery</h1>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Autumn-Leaves-2.png" alt="leaves">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Beard-12.png" alt="beard">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Beard-14.png" alt="beard">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Beard-17.png" alt="beard">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Cigarette-6.png" alt="cancer stick">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Cigarette-13.png" alt="cancer stick">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Dragon-13.png" alt="dragon">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Dragon-14.png" alt="dragon">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Eye-2.png" alt="Eye">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Glasses-52.png" alt="Glasses">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Glasses-63.png" alt="glasses">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Woman-Lips-16.png" alt="woman lips">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Woman-Lips-21.png" alt="lips">
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                    <a class="thumbnail" href="#">
                        <img class="img-responsive" src="style/images/Woman-Lips-24.png" alt="lips">
                    </a>
                </div>
            </div>
            <canvas style="display: none;" id="canvas"></canvas>
        </div>

        <div class="col-sm-12 col-md-3">
            <h2>Pics You've Taken</h2>
            <?php if (isset($user_images))
            {
            foreach ($user_images as $key => $value)
            {?>
            <div class="col-lg-4 col-md-4 col-xs-6 thumb">
                <a class="thumbnail" href="#">
                    <img class="img-responsive" src="<?=$value->file_name;?>" alt="img">
                </a>
                <div style="display: inline-flex;" class="text-center"><a href="#" onclick="del_img(<?=$value->upload_id;?>)">delete</a></div>
            </div>
            <?php }
            }
            ?>
        </div>
    </div>
</div><?php if(isset($_SESSION['user'])){ ?><div id="uid" style="display: none;"><?=$_SESSION['user']->user_id;?></div> <?php } ?>
<footer class="footer">
    <div class="container">
        <p class="text-muted">Copyright lnkadime &copy 2016. </p>
    </div>
</footer>
<script type="text/javascript" src="style/js/script.js"></script>
<script type="text/javascript" src="style/js/home.js"></script>
</body>
</html>