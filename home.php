<?php
session_start();
require_once 'config/CurlClient.class.php';
$curl = new CurlClient(API_URL."?action=general&general=all_img");
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
                <?php if (!isset($_SESSION['user'])){?>
                    <li><a href="login.php">Login<span class="sr-only">(current)</span></a></li>
                    <li><a href="register.php">Register</a></li>
                <?php } ?>
                <?php if (isset($_SESSION['user'])){?>
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="gallery.php">Snap</a></li>
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
        <div class="col-sm-12 col-md-9">
            <div class="row">
                <h2>Pictures</h2>
                <?php
                if (isset($images))
                {
                    foreach ($images as $key => $value)
                    {?>
                        <div class="col-lg-2 col-md-2 col-xs-6 thumb">
                            <a class="thumbnail" href="#">
                                <img onclick="img_more(this, <?=$value->upload_id;?>, <?=$value->likes?>);" class="img-responsive" src="<?=$value->file_name;?>" alt="user_img">
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
                <div id="likes" style="display: none;">
                    <span style="display: none" id="upload_id"></span>
                    <span id="num_likes"></span> likes <?php if (isset($_SESSION['user'])){?><button onclick="like()" class="btn btn-info">Like</button><?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="row"><div id="message_container" style="display: none;" class="text-center alert alert-success" role="alert">
                        <span id="message" ></span>
                    </div>
                </div>
            </div>
            <br>
            <div class="row" id="com" style="display: none;">
                <div id="comments">
                </div>
                <?php if (isset($_SESSION['user'])){?>
                <form class="form-signin" onsubmit="return false;">
                    <label for="comm" class="sr-only">Add Comment</label>
                    <textarea class="form-control" id="comm" placeholder="Add Comment"></textarea>
                    <button id="btn_comment" onclick="comment()" class="btn btn-success">Save</button>
                </form>
                <?php } ?>
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