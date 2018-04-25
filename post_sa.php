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
$img = $_FILES['img'];
$sa_image_path = '';

if($img['name'] == '') {
  // 添付がなければ画像処理はスキップ
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
}

$sa_title = html_escape($_POST['sa_title']);

if($_POST['sa_creator'] == "") {
  $sa_creator = '匿名';
}else{
  $sa_creator = html_escape($_POST['sa_creator']);
}

$sa_twi_id = ltrim(html_escape($_POST['sa_twi_id']), '@,＠');
$sa_content = nl2br(html_escape($_POST['sa_content']));
// $image_path …画像表示用のパス

// ユーザーIDの取得
$user_id = get_user_id($dbh, $_SESSION['random_id']);

// ユーザーIDがなかった場合はユーザーを登録
if($user_id == NULL){
  insert_user($dbh, $_SESSION['random_id']);
  $user_id = get_user_id($dbh, $_SESSION['random_id']);
}


$_SESSION['bosyu_finish'] = $sa_title;

// 作品の登録
insert_sakuhin($dbh, $sa_title, $sa_creator, $sa_twi_id, $sa_content, $sa_image_path, $user_id);
$sa_id = get_new_sa_id($dbh, $user_id);

// 作品ページへ移動
header( 'location: '. SITE_URL.'/sakuhin/s'.$sa_id );
