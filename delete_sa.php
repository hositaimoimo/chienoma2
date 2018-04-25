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

// sa_idなはず
$val = html_escape($_POST['val']);

// 作品に紐づく感想の削除
delete_ka_by_sa($dbh, $val);

// 作品の削除
delete_sa($dbh, $val);
