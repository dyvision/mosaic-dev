<?php
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include('lib/mosaic.php');

use mosaic\top;
use mosaic\user;

$kofi = "<script type='text/javascript' src='https://ko-fi.com/widgets/widget_2.js'></script><script type='text/javascript'>kofiwidget2.init('Support Mosaic on Ko-fi', '#29abe0', 'E1E822CWN');kofiwidget2.draw();</script>";

if (isset($_COOKIE['refresh'])) {
    $header = "<h3 class='navbar-item'><a  onclick='showprofile();'>Profile</a></h3><h3 class='navbar-item'><a  href='api/logout.php'>Logout</a></h3>$kofi";
} else {
    $header = "<h3 class='navbar-item'><a href='authorize.php'>Connect</a></h3>$kofi";
}


echo "<head><link rel='shortcut icon' type='image/png' href='style/MosaicLogo.png'/><title>Mosaic</title><meta name='viewport' content='width=device-width, initial-scale=1'>
<link href='style/style.css' rel='stylesheet'><script src='lib/mosaic.js'></script></head><body onload='getcount();'></br><center><h1>Welcome to Mosaic</h1>$header</br><span>Check out <span id='count'>0</span> users' top songs for the past 4 weeks. Click on a song to listen to it or share yours by clicking connect</span></center></br>";

$usercli = new top();
$lookupcli = new user();


$user = json_decode($usercli->get(null, $_COOKIE['username']), true);
$private = json_decode($lookupcli->get($_COOKIE['username']), true);

echo "<div id='sidebar'><img class='profile' style='width:100%;' src='" . $user['ava'] . "'></img><div class='songlist'><a href='" . $user['url'] . "'><h2>" . $user['display_name'] . "</h2></a>";
foreach ($user['tracks'] as $track) {
    echo "<a href='" . $track['link'] . "'>" . $track['name'] . "</a>";
}

if($private['private']){
    $privatevalue = 'ON';
}else{
    $privatevalue = 'OFF';
}

echo "<h3><a style='display:none'>Groups</a></h3><h3><a onclick='privacytoggle();'>Privacy: $privatevalue</a></h3><h3><a onclick='hideprofile();'>Close</a></h3></div></div><center>";



$users = json_decode(file_get_contents('lists.json'), true);

foreach ($users as $user) {
    $privacy = json_decode($lookupcli->get($user['display_name']), true);
    if ($user == null or $privacy['private'] == true) {
    } else {
        echo "<div class='block'><img class='profile' src='" . $user['ava'] . "'></img><div class='songlist'><a href='" . $user['url'] . "'><h2>" . $user['display_name'] . "</h2></a>";
        foreach ($user['tracks'] as $track) {
            echo "<a href='" . $track['link'] . "'>" . $track['name'] . "</a>";
        }

        echo "</div></div>";
    }
}
echo "</center></body>";
