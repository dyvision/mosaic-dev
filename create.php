<?php
 //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

include('lib/mosaic.php');

use mosaic\auth;
use mosaic\user;

$mosaic = new auth();
$user = new user();

try {
    $token = json_decode($mosaic->authenticate($_GET['code'],'authorization_code'),true);
    $userj = json_decode($mosaic->verify($token['access_token']),true);
    $create = json_decode($user->create($userj['id'],$token['refresh_token']),true);
    shell_exec('curl https://mosaic.paos.io/api/lists.php &');
    if(isset($create['guid'])){
        $mosaic->login($token['access_token'],$token['refresh_token'],$create['guid']);
    } else {
        $mosaic->login($token['access_token'],$token['refresh_token'],$_COOKIE['session']);
    }
    header('location: /home.php');
} catch (Exception $e) {
    header('location: https://mosaic.paos.io/authorize.php');
}
