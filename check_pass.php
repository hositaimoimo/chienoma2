<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once ( __DIR__ . '/functions.php');
require_once ( __DIR__ . '/config.php');

// DBに接続
$dbh = get_db_connect ();

// CSRF対策
$token = $_POST['token'];
check_token($token);

// ka_id, sa_id
$val = html_escape($_POST['val']);
// ka_delete, sa_delete, sa_edit
$check_type = html_escape($_POST['check_type']);
// 入力されたパスワード
$pass = html_escape($_POST['pass']);

switch ($check_type) {
  case 'ka_delete':
    if($pass == get_ka_random_id($dbh, $val)){
      echo "ok";
    }else{
      echo "ng";
    }
    break;

  case 'sa_delete':
  case 'sa_edit':
    if($pass == get_sa_random_id($dbh, $val)){
      echo "ok";
    }else{
      echo "ng";
    }
    break;
  default:
    echo "ng";
    break;
}
