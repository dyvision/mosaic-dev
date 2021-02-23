<?php
include('../lib/mosaic.php');
chdir('../');

use mosaic\auth;

$auth = new auth();
$auth->logout();
header('location: /');