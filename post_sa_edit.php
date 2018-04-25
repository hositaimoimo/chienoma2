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

$edit_sa_id = $_SESSION['edit_sa_id'];
$user_id = get_user_id($dbh, $_SESSION['random_id']);

// キャンセルかどうか
$action = $_POST['action'];

if($action == 'キャンセル'){
  $_SESSION['hensyu_finish'] = NULL;
  header( 'location: '. SITE_URL.'/sakuhin/s'.$edit_sa_id );
  return;
}
// リセット
$_SESSION['edit_sa_id'] = NULL;

// そのユーザーの作品か確認
if($user_id != get_sa_user_id($dbh, $edit_sa_id)){
  // 最後に元のページに戻る
  header( 'location: '. SITE_URL.'/'.$request_url );
}


$sa_title = html_escape($_POST['sa_title']);

if($_POST['sa_creator'] == "") {
  $sa_creator = '匿名';
}else{
  $sa_creator = html_escape($_POST['sa_creator']);
}

$sa_twi_id = ltrim(html_escape($_POST['sa_twi_id']), '@,＠');

$notice = 0;
if($_POST['notice'] == 1){
  $notice = 1;
}

$sa_content = nl2br(html_escape($_POST['sa_content']));
// $image_path …画像表示用のパス


$_SESSION['hensyu_finish'] = $sa_title;

$err = [];
$img = $_FILES['img'];
$sa_image_path = '';

if($img['name'] == '') {
  // 添付がなければ画像処理はスキップ

  // 作品の修正
  update_sakuhin_noimage($dbh, $edit_sa_id, $sa_title, $sa_creator, $sa_twi_id, $sa_content, $notice);

}else{
  // 画像の処理
  $type = exif_imagetype($img['tmp_name']);

  if($type !== IMAGETYPE_JPEG && $type !== IMAGETYPE_PNG){
    // 対象外。何もしない
  }else{
    // リサイズしてjpgで保存
    $sa_image_path = '/user_images/' .md5(uniqid(mt_rand(), true)). '.jpg';
    $output_path = __DIR__. $sa_image_path;
    png2jpg($img['tmp_name'], $output_path, 80);
  }

  // 作品の修正
  update_sakuhin($dbh, $edit_sa_id, $sa_title, $sa_creator, $sa_twi_id, $sa_content, $sa_image_path, $notice);
}

// 作品ページへ移動
header( 'location: '. SITE_URL.'/sakuhin/s'.$edit_sa_id );
