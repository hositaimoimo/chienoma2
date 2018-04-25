<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once ( __DIR__ . '/functions.php');
require_once ( __DIR__ . '/config.php');

// CSRF対策
$token = $_POST['token'];
check_token($token);


$mode = $_POST['mode'];
if($mode == '新着'){
  $mode = 'new';
}elseif($mode == 'HOT'){
  $mode = 'hot';
}elseif($mode == 'My募集'){
  $mode = 'my';
}
$_SESSION['list_mode'] = $mode;


// 作品ページへ移動
header( 'location: '. SITE_URL.'/sakuhin' );
