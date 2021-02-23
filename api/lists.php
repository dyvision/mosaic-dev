<?php
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

chdir('../');
include('lib/mosaic.php');

use mosaic\top;
use mosaic\user;
use mosaic\auth;

$usercli = new user();
$authcli = new auth();
$playlistcli = new top();

$array = array();
$users = json_decode($usercli->get(), true);

foreach ($users as $user) {
    if ($user == null) {
    } else {
        $obj = [];
        $tracks = [];
        $token = json_decode($authcli->authenticate($user['token'], 'refresh_token'), true);
        $username = json_decode($authcli->verify($token['access_token']), true);
        if ($username['images'][0]['url'] == null) {
            $obj['ava'] = 'https://www.tenforums.com/geek/gars/images/2/types/thumb_15951118880user.png';
        } else {
            $obj['ava'] = $username['images'][0]['url'];
        }
        $obj['display_name'] = $username['display_name'];
        $obj['url'] = $username['external_urls']['spotify'];
        $obj['id'] = $username['id'];


        foreach (json_decode($playlistcli->get($token['access_token']), true) as $track) {
            $ftrack['link'] = $track['link'];
            $ftrack['name'] = $track['name']; 
            array_push($tracks,$ftrack);
        }
        $obj['tracks'] = $tracks;
        array_push($array,$obj);
    }
}

$file = fopen('lists.json','w');
fwrite($file,json_encode($array));
fclose($file);

$file = fopen('log.log','a');
fwrite($file,"New List\r\n");
fclose($file);
