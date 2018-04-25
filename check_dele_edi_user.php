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

// valとcheck_typeは改竄可能

// ka_id, sa_id
$val = html_escape($_POST['val']);
// ka_delete, sa_delete, sa_edit
$check_type = html_escape($_POST['check_type']);
// 訪問者のユーザーID
$user_id = get_user_id($dbh, $_SESSION['random_id']);

if($check_type == "ka_delete") {
  // val=ka_id
  // 感想を書いた人か、その募集の主ならOK
  if($user_id == get_ka_user_id($dbh, $val) || $user_id == get_sa_user_id($dbh, get_ka_sa_id($dbh, $val))){
    echo "ok"; //→削除OK
  }else{
    echo "cannot";
  }
}elseif($check_type == "sa_delete" || $check_type == "sa_edit"){
  // val=sa_id
  if($user_id == get_sa_user_id($dbh, $val)) {
    echo "ok";
  }else{
    echo "need_pass";
  }
}
