<?php
include('lib/mosaic.php');

use mosaic\auth;

$mosaic = New auth();
$mosaic->authorize();