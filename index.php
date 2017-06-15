<?php
error_reporting(0);
ini_set('display_errors', 'Off');
set_error_handler(function(){});
$baseurl = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$servername = "DB Server here";
$username = "username here";
$password = "password here";
$dbname = "db name here";
$dbconn = new mysqli($servername, $username, $password, $dbname);
if ($dbconn->connect_error)
{
	die('Database connection failed.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <title>Shrinkr</title>
    <meta name=description content="A URL shortener">
    <meta name=viewport content="width=device-width">
    <link rel="apple-touch-icon" href="icon.png"/>
    <link rel="favicon" href="icon.png"/>
    <link rel="shortcut icon" href="icon.png"/>
    <link rel="icon" href="icon.png"/>
    <link rel=stylesheet href="style.css">
</head>
<body>
<form id="msform" action="#" method="POST">
<fieldset id="login_form">
<h3 class="fs-subtitle">Shrinkr</h3>
<?php
if(isset($_POST['url']))
{
    $shorturl = "";
    $url = trim($_POST['url']);
    if(substr($url,0,4)!="http")
        $url="http://"+$url;
    $query = $dbconn->prepare("SELECT shorturl FROM urldata WHERE url=?");
    $query->bind_param("s", $url);
    $query->execute();
    $query->bind_result($shorturl);
    $query->fetch();
    if($shorturl == null || $shorturl == "")
    {   
        $shorturl=base_convert(rand(10000,99999),20,36);
        $query = $dbconn->prepare("INSERT into urldata (url, shorturl) Values (?, ?)");
        $query->bind_param("ss", $url, $shorturl);
        if($query->execute()!=false)
        {
            $newurl = $baseurl.$shorturl;
            echo "<p>The shortened URL is<br><a href='$newurl'>$newurl</a></p>";
        }
        else
        {
            echo "<p>The URL could not be shortened. Please try again.</p>";
        }
    }
    else
    {
        $newurl = $baseurl.$shorturl;
        echo "<p>The shortened URL is<br><a href='$newurl'>$newurl</a></p>";
    }
}
else if(isset($_GET['url']))
{
    $url = "";
    $shorturl = $_GET['url'];
    $query = $dbconn->prepare("SELECT url FROM urldata WHERE shorturl=?");
    $query->bind_param("s", $shorturl);
    $query->execute();
    $query->bind_result($url);
    $query->fetch();
    if($url == null || $url == "")
    {
        echo "<p>This short URL is not valid.</p>";
    }
    else
    {
        header("Location: ".$url);
    }
}
else
{
?>
<input type="text" name="url" id="url" placeholder="URL here" required />
<button type="submit" id="shrink" class="submit action-button">Shrink URL</button>
<?php
}?>
</fieldset>
</form>
</body>
</html>
