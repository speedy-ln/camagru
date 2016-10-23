<?php
header("Location: home.php");
exit;
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
    <script type="text/javascript" src="style/js/script.js"></script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Camagru</a>
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

</body>
</html>