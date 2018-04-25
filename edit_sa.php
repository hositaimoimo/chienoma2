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

$_SESSION['is_edit_mode'] = TRUE;
$_SESSION['edit_sa_id'] = $val;
