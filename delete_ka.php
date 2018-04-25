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

// ka_idなはず
$val = html_escape($_POST['val']);

// この感想のsa_idを取得
$sa_id = get_ka_sa_id($dbh, $val);
// 感想の削除
delete_ka($dbh, $val);
minus_ka_num($dbh, $sa_id);
