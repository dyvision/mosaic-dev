<?php
include('../lib/mosaic.php');
chdir('../');

use mosaic\user;

$usercli = new user();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $response = [];
        $user = json_decode($usercli->get($_GET['id']),true);
        $response['username'] = $user['username'];
        $response['private'] = $user['private'];
        print_r(json_encode($response));
    } else {
        $userarray = array();

        foreach (json_decode(file_get_contents('lists.json'), true) as $user) {
            if ($user != null) {
                array_push($userarray, $user);
            }
        }
        print_r(json_encode($userarray));
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    $patch = json_decode(file_get_contents('php://input'), true);


    $usercli->update($patch['id'], $patch['guid'],$patch['private']);
}
