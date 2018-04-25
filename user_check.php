<?php
if (!isset($_SESSION)) {
  session_start();
}

$_SESSION['random_id'] = $_POST['random_id'];
