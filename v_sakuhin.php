
<?php

require_once ( __DIR__ . '/twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

// 共通部分を表示…作品のタイトル、作者、画像、コンテンツ、編集、削除

// 現在のsa_idを保存（post_ka.phpで使用）
$_SESSION['now_sa_id'] = $sakuhin['sa_id'];

// 訪問者のユーザーID
$user_id = get_user_id($dbh, $_SESSION['random_id']);
// 募集主のユーザーID
$sa_user_id = $sakuhin['user_id'];

// 訪問者＝募集主ならTRUE
$is_owner = false;
if($user_id == $sa_user_id){
  $is_owner = true;
}

// tweet関連
$tweet_comment1 = "意見募集を手伝う";
$tweet_comment2 = $sakuhin['sa_creator'].'さんが意見を募集しているよ！';
if($is_owner){
  // 自分の募集の場合
  $tweet_comment1 = "フォロワーに知らせる";
  $tweet_comment2 = "ご意見、募集中！";
}

?>

<div class="ad">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- chienoma3 -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-9086924303969740"
       data-ad-slot="3570856499"
       data-ad-format="horizontal"></ins>
  <script>
  (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>

<?php
// 募集完了通知
if($user_id == $sakuhin['user_id'] && $_SESSION['bosyu_finish'] != NULL){
  $_SESSION['bosyu_finish'] = NULL;
?>
  <div class="bs-component">
    <div class="alert alert-dismissible alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>意見募集が公開されました！</p>
    </div>
  </div>
<?php } ?>

<?php
// 修正完了通知
if($user_id == $sakuhin['user_id'] && $_SESSION['hensyu_finish'] != NULL){
  $_SESSION['hensyu_finish'] = NULL;
?>
  <div class="bs-component">
    <div class="alert alert-dismissible alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>募集内容を修正しました！</p>
    </div>
  </div>
<?php } ?>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title"><?php echo $sakuhin['sa_title'] ?></h1>
  </div>
  <div class="panel-body">
    <?php if($sakuhin == NULL){ ?>
      <p>意見募集中のものが見つかりませんでした。</p>
    <?php }else{ ?>
      <?php
        // ツイート埋め込み用
        // 内容からurlの抽出
        preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $sakuhin['sa_content'], $urls);
        if(empty($urls[0])){
          // URLがないので何もしない
        }else {
          foreach ($urls[0] as $row) {
            if(preg_match('|^https://twitter.com+[/]{1}[a-zA-Z0-9_]+[/]{1}+status+[/]{1}[0-9]+$|', $row)){
              // 埋め込めそうなURL→埋め込み用のタグに変更
              // 未接続なら接続
              if($connection == NULL){
                $oauth_access_token = ACCESS_TOKEN;
                $oauth_access_token_secret = ACCESS_TOKEN_SECRET;
                $consumer_key = CONSUMER_KEY;
                $consumer_secret = CONSUMER_SECRET;
                $connection = new TwitterOAuth($consumer_key,$consumer_secret,$oauth_access_token,$oauth_access_token_secret);
              }
              $ret = $connection->get("statuses/oembed", array("url" => $row));
              $sakuhin['sa_content'] = str_replace($row, $ret->html, $sakuhin['sa_content']);
            }else{
              // URLをリンクに置換
              $replace_str = '<a href="'.$row.'" target="_blank">'.$row.'</a>';
              $sakuhin['sa_content'] = str_replace($row, $replace_str, $sakuhin['sa_content']);
            }
          }

        }
        // tweet埋め込み処理ここまで
      ?>

      <div class="sa_obj sa_creator">
        <?php if($sakuhin['sa_twi_id'] != ""){ ?>
        <p>募集主：<a href="https://twitter.com/<?php echo $sakuhin['sa_twi_id'] ?>" target="_blank"><?php echo $sakuhin['sa_creator'] ?></a></p>
      <?php }else{ ?>
        <p>募集主：<?php echo $sakuhin['sa_creator'] ?></p>
      <?php } ?>
      </div>

      <?php
      if($sakuhin['sa_image_path'] != ""){
      ?>
        <div class="sa_obj sakuhin_image">
          <img src="<?php echo $sakuhin['sa_image_path'] ?>">
        </div>
      <?php } ?>

      <div class="sa_obj sa_content">
  <!--      <p><?php echo mb_ereg_replace('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', '<a href="\1" target="_blank">\1</a>', $sakuhin['sa_content']); ?> -->
        <p><?php echo $sakuhin['sa_content']; ?></p>
      </div>
    <?php } ?>

    <div class="icons">
      <button class="delete_edit_btn btn btn-warning" name="sa_edit" value="<?php echo $sakuhin['sa_id'].','.$token; ?>"><i class="fa fa-edit"></i></button>
      <button class="delete_edit_btn btn btn-danger" name="sa_delete" value="<?php echo $sakuhin['sa_id'].','.$token; ?>"><i class="fa fa-trash-o"></i></button>
    </div>
  </div>
</div>

<?php
// 訪問者のIDと作品のuser_idが同じならパスワードを表示
if($is_owner){
?>
  <div class="bs-component">
    <div class="alert alert-dismissible alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>募集時と異なるブラウザから編集を行うにはパスワードが必要となります。</p>
      <p>パスワード：<input type="text" size="11" value="<?php echo $_SESSION['random_id'] ?>" onfocus="this.select();" readonly></p>
    </div>
  </div>
<?php } ?>

<a class="btn twitter twi_btn" target="_blank"
 href="http://twitter.com/share?url=<?php echo SITE_URL.'/'.$request_url; ?>&text=<?php echo $tweet_comment2; ?>%0a『<?php echo $sakuhin['sa_title']; ?>』%0a&hashtags=chienoma">
 <i class="fa fa-lg fa-twitter"></i>&nbsp;<?php echo $tweet_comment1 ?>（ツイート）
</a>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title">意見・感想・応援などを書く</h1>
  </div>
  <div class="panel-body">
    <?php if($sakuhin == NULL){ ?>
      <p>意見募集中のものが見つかりませんでした。</p>
    <?php }else{ ?>
      <form action="<?php echo SITE_URL.'/post_ka.php' ?>" method="POST">
        <p><textarea class="input_area" name="ka_comment" rows="5" cols="20" maxlength="500" required></textarea></p>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="ka_hidden" value="1"> 募集主にのみ公開
          </label>
        </div>

        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <p class="center comment_button"><input type="submit" class="btn btn-success" value="書き込む"></p>
      </form>
    <?php } ?>
  </div>
</div>

<div class="ad">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- chienoma4 -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-9086924303969740"
       data-ad-slot="4967698640"
       data-ad-format="horizontal"></ins>
  <script>
  (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title" id="comment">寄せられた意見（<?php echo count($kansou_list) ?>件）</h1>
  </div>
  <div class="panel-body">
    <?php if($kansou_list == NULL){ ?>
      <p>寄せられた意見はまだありません。</p>
    <?php }else{?>
      <ul class="kansou_list more_list">
        <?php
        foreach(array_reverse($kansou_list) as $row){
        ?>
          <li class="kansou_li" id="kansou<?php echo $row['ka_id']; ?>">
            <div class="ka_comment word_wrap">
              <?php if($row['ka_hidden'] == 1) {
                if($is_owner || $user_id == $row['user_id']){ ?>
                  <p><?php echo mb_ereg_replace('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', '<a href="\1" target="_blank">\1</a>', $row['ka_comment']); ?></p>
                <?php }else{ ?>
                  <p>※募集主のみ閲覧可</p>
                <?php }
              }else{ ?>
                <p><?php echo mb_ereg_replace('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', '<a href="\1" target="_blank">\1</a>', $row['ka_comment']); ?></p>
              <?php } ?>
            </div>
            <div class="row">
              <div class="col-xs-8">
                <small><?php echo date('Y年m月d日 H:i', strtotime($row['ka_created'])); ?></small>
              </div>
              <div class="col-xs-4">
                <?php if($is_owner || $user_id == $row['user_id']) {?>
                <div class="icons">
                  <button class="delete_edit_btn btn btn-danger" name="ka_delete" value="<?php echo $row['ka_id'].','.$token; ?>"><i class="fa fa-trash-o"></i></button>
                </div>
              <?php } ?>
              </div>
            </div>

          </li>
        <?php } ?>
      </ul>
      <p id="more_btn" class="jump-btn btn btn-default"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;もっと表示</p>
    <?php } ?>
  </div>
</div>

<a class="btn twitter twi_btn" target="_blank"
 href="http://twitter.com/share?url=<?php echo SITE_URL.'/'.$request_url; ?>&text=<?php echo $tweet_comment2; ?>%0a『<?php echo $sakuhin['sa_title']; ?>』%0a&hashtags=chienoma">
 <i class="fa fa-lg fa-twitter"></i>&nbsp;<?php echo $tweet_comment1 ?>（ツイート）
</a>

<div class="ad">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- chienoma5 -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-9086924303969740"
       data-ad-slot="8440039799"
       data-ad-format="auto"></ins>
  <script>
  (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>

<div class="jump-buttons">
  <p><a class="jump-btn btn btn-danger" href="<?php echo SITE_URL. '/bosyu'; ?>">意見を募集ページを作成</a></p>
  <p><a class="jump-btn btn btn-primary" href="<?php echo SITE_URL. '/sakuhin'; ?>">意見募集中リストを見る</a></p>
</div>
