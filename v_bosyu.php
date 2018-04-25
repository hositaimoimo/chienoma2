<?php
if($_SESSION['is_edit_mode'] == TRUE){
  ///////////////////////////////////////////////////////////////////
  // 編集モード
  ///////////////////////////////////////////////////////////////////

  // 編集する作品のID
  $edit_sa_id = $_SESSION['edit_sa_id'];
  // 作品の取得
  $edit_sakuhin = get_sakuhin($dbh, $edit_sa_id);

  // フラグのリセット（edit_sa_idはpost_sa_editでも使うため残す）
  $_SESSION['is_edit_mode'] = NULL;

?>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title">募集ページの修正</h1>
  </div>
  <div class="panel-body">
    <form action="<?php echo SITE_URL.'/post_sa_edit.php' ?>" enctype="multipart/form-data" method="POST">
      <!-- 作品名入力フォーム -->
      <p class="bosyu_menu">募集タイトル</p>
      <small>このイラストの感想ください。このダジャレの評価をお願いします。このアイデアどう思いますか。等々</small>
      <p><input class="input_area" type="text" name="sa_title" value="<?php echo $edit_sakuhin['sa_title'] ?>" required></p>

      <!-- 募集者名入力フォーム -->
      <p class="bosyu_menu">募集主名（なくてもOK）</p>
      <small>空欄の場合は「匿名」になります。</small>
      <p><input class="input_area" type="text" name="sa_creator" placeholder="匿名" value="<?php echo $edit_sakuhin['sa_creator'] ?>"></p>

      <!-- Twitter ID入力フォーム -->
      <p class="bosyu_menu">Twitter ID（なくてもOK）</p>
      <small>募集主名にリンクが生成されます。</small>
      <p><input class="input_area num-input" type="text" name="sa_twi_id" placeholder="＠twitter_id" value="<?php echo $edit_sakuhin['sa_twi_id'] ?>"></p>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="notice" value="1" <?php if($edit_sakuhin['notice'] == 1){echo 'checked';} ?>> 意見が寄せられた時に<a href="https://twitter.com/chienomanet" target="_blank">公式Twitter</a>からのメンション（通知）を受け取る
        </label>
      </div>
      <!-- 画像入力フォーム -->
      <p class="bosyu_menu">添付画像（なくてもOK）</p>
      <small>.pngまたは.jpgのみ。投稿後は最大600×600pxのjpg画像になります。</small>
      <div class="input_area preview">
        <?php if($edit_sakuhin['sa_image_path'] != ""){ ?>
          <img src="<?php echo $edit_sakuhin['sa_image_path'] ?>">
        <?php } ?>
      </div>
      <div class="center image_button_frame input_area">
        <label class="select_image_button" for="file_image">＋画像を選択</label>

        <p><input type="file" id="file_image" name="img" style="display:none;"></p>
      </div>

      <!-- 内容入力フォーム -->
      <p class="bosyu_menu">内容</p>
      <small>見てほしい文章、アイデア等々。URLは自動でリンク付与されます。</small>
      <p><textarea class="input_area" name="sa_content" rows="5" cols="20" maxlength="500" required><?php echo strip_tags($edit_sakuhin['sa_content']) ?></textarea></p>

      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <div class="row">
        <div class="col-xs-6 pad0">
      <p class="center regist_button"><input type="submit" name="action" class="btn btn-default" value="キャンセル"></p>
        </div>
        <div class="col-xs-6 pad0">
      <p class="center regist_button"><input type="submit" name="action" class="btn btn-success" value="修正する"></p>
        </div>
      </div>
    </form>
  </div>
</div>



<?php }else{
  ///////////////////////////////////////////////////////////////////
  // 通常モード
  ///////////////////////////////////////////////////////////////////
?>

<div class="panel panel-success panel_main">
  <div class="panel-heading">
    <h1 class="panel-title">意見募集ページの作成</h1>
  </div>
  <div class="panel-body">
    <form action="<?php echo SITE_URL.'/post_sa.php' ?>" enctype="multipart/form-data" method="POST">
      <!-- 作品名入力フォーム -->
      <p class="bosyu_menu">募集タイトル</p>
      <small>このイラストの感想ください。このダジャレの評価をお願いします。このアイデアどう思いますか。等々</small>
      <p><input class="input_area" type="text" name="sa_title" required></p>

      <!-- 募集者名入力フォーム -->
      <p class="bosyu_menu">募集主名（なくてもOK）</p>
      <small>空欄の場合は「匿名」になります。</small>
      <p><input class="input_area" type="text" name="sa_creator" placeholder="匿名"></p>

      <!-- Twitter ID入力フォーム -->
      <p class="bosyu_menu">Twitter ID（なくてもOK）</p>
      <small>募集主名にリンクが生成されます。</small>
      <p><input class="input_area num-input" type="text" name="sa_twi_id" placeholder="＠twitter_id"></p>
      <div class="checkbox">
        <label>
          <input type="checkbox" name="notice" value="1"> 意見が寄せられた時に<a href="https://twitter.com/chienomanet" target="_blank">公式Twitter</a>からのメンション（通知）を受け取る
        </label>
      </div>
      <!-- 画像入力フォーム -->
      <p class="bosyu_menu">添付画像（なくてもOK）</p>
      <small>.pngまたは.jpgのみ。投稿後は最大600×600pxのjpg画像になります。</small>
      <div class="input_area preview"></div>
      <div class="center image_button_frame input_area">
        <label class="select_image_button" for="file_image">＋画像を選択</label>

        <p><input type="file" id="file_image" name="img" style="display:none;"></p>
      </div>

      <!-- 内容入力フォーム -->
      <p class="bosyu_menu">内容</p>
      <small>見てほしい文章、アイデア等々。URLは自動でリンク付与されます。</small>
      <p><textarea class="input_area" name="sa_content" rows="5" cols="20" maxlength="500" required></textarea></p>

      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <p class="center regist_button"><input type="submit" class="btn btn-success" value="完成！"></p>
      <p class="small center">※募集ページは後から修正可能です。</p>
    </form>
  </div>
</div>

<?php } ?>
