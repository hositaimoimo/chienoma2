<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once ( __DIR__ . '/functions.php');
require_once ( __DIR__ . '/config.php');

$dbh = get_db_connect ();

$request_url = $_SESSION['request_url'];

// CSRF対策
$token = $_POST['token'];
check_token($token);

// ユーザーIDが定まっていない場合
if($_SESSION['random_id'] == NULL){
  // 最後に元のページに戻る
  header( 'location: '. SITE_URL.'/'.$request_url );
  // 余裕があればエラー表示
}

$err = [];

// ユーザーIDの取得
$user_id = get_user_id($dbh, $_SESSION['random_id']);

// ユーザーIDがなかった場合はユーザーを登録
if($user_id == NULL){
  insert_user($dbh, $_SESSION['random_id']);
  $user_id = get_user_id($dbh, $_SESSION['random_id']);
}

$sa_id = $_SESSION['now_sa_id'];
$ka_comment = nl2br(html_escape($_POST['ka_comment']));
$ka_hidden = 0;
if (isset($_POST['ka_hidden'])) {
    if(html_escape($_POST['ka_hidden']) == '1'){
      $ka_hidden = 1;
    }else{
      $ka_hidden = 0;
    }
}

// 感想の登録
insert_kansou($dbh, $sa_id, $user_id, $ka_comment, $ka_hidden);
plus_ka_num($dbh, $sa_id);

// リセット
$_SESSION['now_sa_id'] = NULL;

// 作品ページへ移動
header( 'location: '. SITE_URL.'/'.$request_url.'#comment' );
