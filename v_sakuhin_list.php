<?php
// 募集削除通知
if($user_id == $sakuhin['user_id'] && $_SESSION['bosyu_finish'] != NULL){
  $_SESSION['bosyu_finish'] = NULL;
?>
  <div class="bs-component">
    <div class="alert alert-dismissible alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <p>意見募集を削除しました</p>
    </div>
  </div>
<?php } ?>

<div class="jump-buttons">
  <p><a class="jump-btn btn btn-danger" href="<?php echo SITE_URL. '/bosyu'; ?>">意見募集ページを作成</a></p>
</div>

<div class="ad">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- chienoma1 -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-9086924303969740"
       data-ad-slot="6650237984"
       data-ad-format="horizontal"></ins>
  <script>
  (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>

<div class="row">
  <div class="col-xs-4">
    <form action="<?php echo SITE_URL.'/post_sa_list_mode.php' ?>" method="POST">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="submit" class="jump-btn btn btn-success" name="mode" value="新着">
    </form>
  </div>
  <div class="col-xs-4">
    <form action="<?php echo SITE_URL.'/post_sa_list_mode.php' ?>" method="POST">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="submit" class="jump-btn btn btn-warning" name="mode" value="HOT">
    </form>
  </div>
  <div class="col-xs-4">
    <form action="<?php echo SITE_URL.'/post_sa_list_mode.php' ?>" method="POST">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="submit" class="jump-btn btn btn-primary" name="mode" value="My募集">
    </form>
  </div>
</div>
<?php if($_SESSION['list_mode'] == 'hot') { ?>

  <div class="panel panel-success panel_main">
    <div class="panel-heading">
      <h1 class="panel-title">意見募集中リスト（HOT）</h1>
    </div>
    <div class="panel-body">
      <?php if($sakuhin_list == NULL){ ?>
        <p>意見募集中のものがありません。</p>
      <?php }else{?>
        <ul class="sakuhin_list more_list">
          <?php

          // 今の時刻を取得
          $datetime1 = new DateTime();
          for($i = 0; $i < count($sakuhin_list); $i++){
            // 作成日を取得
            $datetime2 = new Datetime($sakuhin_list[$i]['sa_created']);
            // 日数差を計算
            $diff = $datetime1->diff($datetime2);
            // 意見数から日数差を引いてスコアを算出
            $hot_score = $sakuhin_list[$i]['ka_num'] + $diff->format('%R%a');
            // 配列に追加
            $sakuhin_list[$i] = array_merge($sakuhin_list[$i],array('hot_score'=> $hot_score));
          }

          $sakuhin_list = array_reverse($sakuhin_list);

          // 列方向の配列を得る
          foreach ($sakuhin_list as $key => $row) {
              $ka_num[$key]  = $row['hot_score'];
          }
          // ka_numをキーにして並び替え
          array_multisort($ka_num, SORT_DESC, $sakuhin_list);

          foreach($sakuhin_list as $row){
          ?>
            <li class="sakuhin_li" id="sakuhin<?php echo $row['sa_id']; ?>">
              <div class="panel panel-default panel_sub">
                <div class="panel-heading">
                  <div>
                    <h2 class="panel-title"><?php echo $row['sa_title'].'（'. $row['ka_num'].'）'; ?></h2>
                  </div>
                </div>
                <div class="panel-body sakuhin-body">
                  <?php
                  if($row['sa_image_path'] != ""){
                  ?>
                    <div class="sa_obj sakuhin_image">
                      <img src="<?php echo $row['sa_image_path'] ?>">
                    </div>
                  <?php } ?>
                  <div class="word_wrap">
                    <?php echo mb_strimlen($row['sa_content'], 0, 50, "..."); ?>
                  </div>
                </div>
              </div>
              <div class="arrow">
                <p><i class="fa fa-chevron-right" aria-hidden="true"></i></p>
              </div>
              <a class="sakuhin_link" href="<?php echo SITE_URL.'/sakuhin/s'.$row['sa_id'] ?>"></a>
            </li>
          <?php } ?>
        </ul>
        <p id="more_btn" class="jump-btn btn btn-default"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;もっと表示</p>
      <?php } ?>
    </div>
  </div>

<?php }elseif($_SESSION['list_mode'] == 'my') { ?>

  <div class="panel panel-success panel_main">
    <div class="panel-heading">
      <h1 class="panel-title">意見募集中リスト（My募集）</h1>
    </div>
    <div class="panel-body">
      <?php if($sakuhin_list == NULL){ ?>
        <p>意見募集中のものがありません。</p>
      <?php }else{?>
        <ul class="sakuhin_list more_list">
          <?php

          // 訪問者のユーザーID
          $user_id = get_user_id($dbh, $_SESSION['random_id']);

          foreach(array_reverse($sakuhin_list) as $row){
            if($user_id != $row['user_id']){
              continue;
            }
          ?>
            <li class="sakuhin_li" id="sakuhin<?php echo $row['sa_id']; ?>">
              <div class="panel panel-default panel_sub">
                <div class="panel-heading">
                  <div>
                    <h2 class="panel-title"><?php echo $row['sa_title'].'（'. $row['ka_num'].'）'; ?></h2>
                  </div>
                </div>
                <div class="panel-body sakuhin-body">
                  <?php
                  if($row['sa_image_path'] != ""){
                  ?>
                    <div class="sa_obj sakuhin_image">
                      <img src="<?php echo $row['sa_image_path'] ?>">
                    </div>
                  <?php } ?>
                  <div class="word_wrap">
                    <?php echo mb_strimlen($row['sa_content'], 0, 50, "..."); ?>
                  </div>
                </div>
              </div>
              <div class="arrow">
                <p><i class="fa fa-chevron-right" aria-hidden="true"></i></p>
              </div>
              <a class="sakuhin_link" href="<?php echo SITE_URL.'/sakuhin/s'.$row['sa_id'] ?>"></a>
            </li>
          <?php } ?>
        </ul>
        <p id="more_btn" class="jump-btn btn btn-default"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;もっと表示</p>
      <?php } ?>
    </div>
  </div>

<?php }else{ ?>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title">意見募集中リスト（新着）</h1>
  </div>
  <div class="panel-body">
    <?php if($sakuhin_list == NULL){ ?>
      <p>意見募集中のものがありません。</p>
    <?php }else{?>
      <ul class="sakuhin_list more_list">
        <?php
        foreach(array_reverse($sakuhin_list) as $row){
        ?>
          <li class="sakuhin_li" id="sakuhin<?php echo $row['sa_id']; ?>">
            <div class="panel panel-default panel_sub">
              <div class="panel-heading">
                <div>
                  <h2 class="panel-title"><?php echo $row['sa_title'].'（'. $row['ka_num'].'）'; ?></h2>
                </div>
              </div>
              <div class="panel-body sakuhin-body">
                <?php
                if($row['sa_image_path'] != ""){
                ?>
                  <div class="sa_obj sakuhin_image">
                    <img src="<?php echo $row['sa_image_path'] ?>">
                  </div>
                <?php } ?>
                <div class="word_wrap">
                  <?php echo mb_strimlen($row['sa_content'], 0, 50, "..."); ?>
                </div>
              </div>
            </div>
            <div class="arrow">
              <p><i class="fa fa-chevron-right" aria-hidden="true"></i></p>
            </div>
            <a class="sakuhin_link" href="<?php echo SITE_URL.'/sakuhin/s'.$row['sa_id'] ?>"></a>
          </li>
        <?php } ?>
      </ul>
      <p id="more_btn" class="jump-btn btn btn-default"><i class="fa fa-angle-double-down" aria-hidden="true"></i>&nbsp;もっと表示</p>
    <?php } ?>
  </div>
</div>

<?php } ?>

<div class="ad">
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
  <!-- chienoma2 -->
  <ins class="adsbygoogle"
       style="display:block"
       data-ad-client="ca-pub-9086924303969740"
       data-ad-slot="6623561098"
       data-ad-format="auto"></ins>
  <script>
  (adsbygoogle = window.adsbygoogle || []).push({});
  </script>
</div>

<div class="jump-buttons">
  <p><a class="jump-btn btn btn-danger" href="<?php echo SITE_URL. '/bosyu'; ?>">意見募集ページを作成</a></p>
</div>
