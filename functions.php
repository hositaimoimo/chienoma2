<?php
// html escape
function html_escape($word) {
  return htmlspecialchars($word, ENT_QUOTES, 'UTF-8');
}
// DB接続
function get_db_connect() {
  try {
    $dsn = DSN;
    $user = DB_USER;
    $password = DB_PASSWORD;
    $dbh = new PDO($dsn, $user, $password);
  }catch(PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  $dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}
// 文字数を丸める
function mb_strimlen($str, $start, $length, $trimmarker = '', $encoding = false) {
   $encoding = $encoding ? $encoding : mb_internal_encoding();
   $str = mb_substr($str, $start, mb_strlen($str), $encoding);
   if (mb_strlen($str, $encoding) > $length) {
       $markerlen = mb_strlen($trimmarker, $encoding);
       $str = mb_substr($str, 0, $length - $markerlen, $encoding) . $trimmarker;
   }
   return $str;
}
// CSRF対策
function set_token() {
  $token = sha1(uniqid(mt_rand(), true));
  $_SESSION['token'] = $token;
  return $token;
}
function check_token($token) {
  if(empty($_SESSION['token']) || ($_SESSION['token'] != html_escape($token))) {
    header( 'location: '. SITE_URL);
    exit;
  }
}


///////////////////////////////////////////////////
// 画像関連
///////////////////////////////////////////////////
function png2jpg($img_path, $output_path, $quality) {
  // 加工前の画像の情報を取得
  list($original_w, $original_h, $type) = getimagesize($img_path);
  // png と jpg 以外のケースは事前に弾いてある
  if ($type == IMAGETYPE_PNG){
    $original_image = imagecreatefrompng($img_path);
  } elseif ($type == IMAGETYPE_JPEG) {
    $original_image = imagecreatefromjpeg($img_path);
  }
  $w = IMAGE_WIDTH;
  $h = IMAGE_HEIGHT;
  if($original_h >= $original_w){
    $w = IMAGE_WIDTH * $original_w / $original_h;
  }else{
    $h = IMAGE_HEIGHT * $original_h / $original_w;
  }
  $canvas = imagecreatetruecolor($w, $h);
  imagecopyresampled($canvas, $original_image, 0,0,0,0, $w, $h, $original_w, $original_h);
  imagejpeg($canvas, $output_path, $quality);
  imagedestroy($original_image);
  // クオリティは 0 (一番圧縮されています) から 100 (高画質)の間の値です。
}


///////////////////////////////////////////////////
// ユーザー関連
///////////////////////////////////////////////////

// ユーザーをDBに登録
function insert_user($dbh, $random_id) {
  try {
    $sql = "INSERT INTO user (random_id) VALUE (:random_id)";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':random_id', $random_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}
// ユーザーIDの取得
function get_user_id($dbh, $random_id) {
  try {
    $sql = "SELECT * FROM user WHERE random_id = :random_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':random_id', $random_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['user_id'];
}

// ka_idからのrandom_idの取得
function get_ka_random_id($dbh, $ka_id) {
  try {
    $sql = "SELECT * FROM user INNER JOIN kansou ON user.user_id = kansou.user_id WHERE ka_id = :ka_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':ka_id', $ka_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['random_id'];
}

// sa_idからのrandom_idの取得
function get_sa_random_id($dbh, $sa_id) {
  try {
    $sql = "SELECT * FROM user INNER JOIN sakuhin ON user.user_id = sakuhin.user_id WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['random_id'];
}

///////////////////////////////////////////////////
// 作品関連
///////////////////////////////////////////////////

// 作品をDBに登録
function insert_sakuhin($dbh, $sa_title, $sa_creator, $sa_twi_id, $sa_content, $sa_image_path, $user_id) {
  $date = date('Y-m-d H:i:s');
  try {
    $sql = "INSERT INTO sakuhin (sa_title, sa_creator, sa_twi_id, sa_content, sa_image_path, user_id, sa_created)
     VALUE (:sa_title, :sa_creator, :sa_twi_id, :sa_content, :sa_image_path, :user_id, '{$date}')";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_title', $sa_title, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_creator', $sa_creator, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_twi_id', $sa_twi_id, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_content', $sa_content, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_image_path', $sa_image_path, PDO::PARAM_STR);
    $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}
// 最新の自分の作品IDの取得
function get_new_sa_id($dbh, $user_id) {
  try {
    $sql = "SELECT MAX(sa_id) FROM sakuhin WHERE user_id = :user_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['MAX(sa_id)'];
}

// 作品の取得
function get_sakuhin($dbh, $sa_id) {
  try {
    $sql = "SELECT * FROM sakuhin WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
    $sakuhin = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $sakuhin;
}

// 作品リストの取得
function get_sakuhin_list($dbh) {
  try {
    $sql = "SELECT * FROM sakuhin";
    $stmt = $dbh -> prepare($sql);
    $stmt -> execute();
    $sakuhin_list = [];
    $count = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $sakuhin_list[] = $row;
    }
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $sakuhin_list;
}
// 作品のuser_idの取得
function get_sa_user_id($dbh, $sa_id){
  try {
    $sql = "SELECT * FROM sakuhin WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['user_id'];
}

// 作品の修正（画像あり）
function update_sakuhin($dbh, $sa_id, $sa_title, $sa_creator, $sa_twi_id, $sa_content, $sa_image_path) {
  try {
    $sql = "UPDATE sakuhin SET sa_title = :sa_title, sa_creator = :sa_creator, sa_twi_id = :sa_twi_id, sa_content = :sa_content, sa_image_path = :sa_image_path
     WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_title', $sa_title, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_creator', $sa_creator, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_twi_id', $sa_twi_id, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_content', $sa_content, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_image_path', $sa_image_path, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 作品の修正（画像なし）
function update_sakuhin_noimage($dbh, $sa_id, $sa_title, $sa_creator, $sa_twi_id, $sa_content) {
  try {
    $sql = "UPDATE sakuhin SET sa_title = :sa_title, sa_creator = :sa_creator, sa_twi_id = :sa_twi_id, sa_content = :sa_content
     WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_title', $sa_title, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_creator', $sa_creator, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_twi_id', $sa_twi_id, PDO::PARAM_STR);
    $stmt -> bindValue(':sa_content', $sa_content, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 感想数のプラス
function plus_ka_num($dbh, $sa_id){
  try {
    $sql = "UPDATE sakuhin SET ka_num = ka_num+1 WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 感想数のマイナス
function minus_ka_num($dbh, $sa_id){
  try {
    $sql = "UPDATE sakuhin SET ka_num = ka_num-1 WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}


///////////////////////////////////////////////////
// 感想関連
///////////////////////////////////////////////////

// 感想をDBに登録
function insert_kansou($dbh, $sa_id, $user_id, $ka_comment, $ka_hidden) {
  $date = date('Y-m-d H:i:s');
  try {
    $sql = "INSERT INTO kansou (sa_id, user_id, ka_comment, ka_hidden, ka_created)
     VALUE (:sa_id, :user_id, :ka_comment, :ka_hidden, '{$date}')";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $stmt -> bindValue(':ka_comment', $ka_comment, PDO::PARAM_STR);
    $stmt -> bindValue(':ka_hidden', $ka_hidden, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 作品を削除
function delete_sa($dbh, $sa_id){
  try {
    $sql = "DELETE FROM sakuhin WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 感想リストの取得
function get_kansou_list($dbh, $sa_id) {
  try {
    $sql = "SELECT * FROM kansou WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
    $kansou_list = [];
    $count = $stmt->rowCount();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      $kansou_list[] = $row;
    }
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $kansou_list;
}

// 感想のユーザーIDの取得
function get_ka_user_id($dbh, $ka_id) {
  try {
    $sql = "SELECT * FROM kansou WHERE ka_id = :ka_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':ka_id', $ka_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['user_id'];
}

// 感想のsa_idの取得
function get_ka_sa_id($dbh, $ka_id) {
  try {
    $sql = "SELECT * FROM kansou WHERE ka_id = :ka_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':ka_id', $ka_id, PDO::PARAM_STR);
    $stmt -> execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
  return $result['sa_id'];
}

// 感想を削除
function delete_ka($dbh, $ka_id){
  try {
    $sql = "DELETE FROM kansou WHERE ka_id = :ka_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':ka_id', $ka_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}

// 作品削除に伴う感想削除
function delete_ka_by_sa($dbh, $sa_id){
  try {
    $sql = "DELETE FROM kansou WHERE sa_id = :sa_id";
    $stmt = $dbh -> prepare($sql);
    $stmt -> bindValue(':sa_id', $sa_id, PDO::PARAM_STR);
    $stmt -> execute();
  }catch (PDOException $e) {
    echo($e -> getMessage());
    die();
  }
}
