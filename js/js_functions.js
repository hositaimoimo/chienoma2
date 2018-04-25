// ユーザー確認→(パス入力)→削除確認→削除
$(document).on('click', '.delete_edit_btn', function() {
  var vals = $(this).val().split(',');
  var val = vals[0];
  var token = vals[1];
  var check_type = $(this).attr("name");
  // check_typeが以上値な場合は何もしない
  if(check_type != "ka_delete" && check_type != "sa_delete" && check_type != "sa_edit"){
    console.log('error');
    return;
  }
  $.ajax({
    type: "POST",
    url: "//chienoma.net/check_dele_edi_user.php",
    data: {
      "val": val, // ka_id または sa_id
      "check_type": check_type, // ka_delete, sa_delete, sa_edit
      "token": token
    }
  })
  .then(
    function (data) {
      // 1つめは通信成功時のコールバック
      // ユーザーチェックの結果が返ってくる（sa_owner, ka_owner, need_pass, cannot
      console.log(data);
      if(data == "cannot") {
        // ありえないはず。
      }else if(data == "ok") {
        // check_typeで振り分け
        if(check_type == 'ka_delete'){
          swal({
            title: '意見を削除します',
            text: '削除した意見は復元できません。削除してよろしいですか？',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: '削除する',
            cancelButtonText: "キャンセル",
            closeOnConfirm: false
          },
          function(isComfirm){
            if(isComfirm){
              // 意見を削除
              $.ajax({
                type: "POST",
                url: "//chienoma.net/delete_ka.php",
                data: {
                  "val": val, // ka_idなはず
                  "token": token
                }
              })
              .then(
                function (data) {
                  // 1つめは通信成功時のコールバック
                  // var id = '#kansou' + val;
                  // $(id).hide();
                  // swal("意見を削除しました", "", "success");
                  location.reload();
                },
                function (data) {
                  // 2つめは通信失敗時のコールバック
                  console.log(data);
                  swal("エラーが発生しました。", "", "success");
                }
              );
            }else{
              // 入力値がない場合…何もしない
            }
          });
        }else if(check_type == 'sa_delete'){
          swal({
            title: '募集を削除します',
            text: '削除すると復元できません。削除してよろしいですか？',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: '削除する',
            cancelButtonText: "キャンセル",
            closeOnConfirm: false
          },
          function(isComfirm){
            if(isComfirm){
              // 意見募集を削除
              $.ajax({
                type: "POST",
                url: "//chienoma.net/delete_sa.php",
                data: {
                  "val": val, // sa_idなはず
                  "token": token
                }
              })
              .then(
                function (data) {
                  // 1つめは通信成功時のコールバック
                  window.location.href = "//chienoma.net/sakuhin";
                },
                function (data) {
                  // 2つめは通信失敗時のコールバック
                  swal("エラーが発生しました。", "", "success");
                }
              );
            }else{
              // 入力値がない場合…何もしない
            }
          });
        }else if(check_type == 'sa_edit'){
          $.ajax({
            type: "POST",
            url: "//chienoma.net/edit_sa.php",
            data: {
              "val": val, // sa_idなはず
              "token": token
            }
          })
          .then(
            function (data) {
              // 1つめは通信成功時のコールバック
              window.location.href = "//chienoma.net/bosyu";
            },
            function (data) {
              // 2つめは通信失敗時のコールバック
              swal("エラーが発生しました。", "", "success");
            }
          );
        }else{
          swal("エラーが発生しました。", "", "success");
        }


      }else if(data == "need_pass"){
        swal({
          title: 'パスワードを入力してください',
          text: '',
          type: 'input',
          showCancelButton: true,
          confirmButtonColor: '#DD6B55',
          cancelButtonText: 'キャンセル',
          closeOnConfirm: false,
          content: 'input_data'
        },
        function(isComfirm){
          if(isComfirm){
            // 入力値がある場合
            console.log(isComfirm);
            // ajaxからまるっとコピペしてパスチェック.phpで判定、結果を受けて何かしら表示。
            // 感想削除or募集削除or募集編集。phpを3パターンに
            $.ajax({
              type: "POST",
              url: "//chienoma.net/check_pass.php",
              data: {
                "val": val, // ka_id または sa_id
                "check_type": check_type, // ka_delete, sa_delete, sa_edit
                "pass": isComfirm, // 入力されたパスワード
                "token": token
              }
            })
            .then(
              function (data) {
                // 1つめは通信成功時のコールバック
                // ユーザーチェックの結果が返ってくる（ok, ng)
                console.log(data);
                if(data == "ng") {
                  swal("パスワードが違います。", "", "success");
                }else if(data == "ok") {
                  // check_typeで振り分け
                  if(check_type == 'ka_delete'){
                    swal({
                      title: '意見を削除します',
                      text: '削除した意見は復元できません。削除してよろしいですか？',
                      type: "warning",
                      showCancelButton: true,
                      confirmButtonColor: '#DD6B55',
                      confirmButtonText: '削除する',
                      cancelButtonText: "キャンセル",
                      closeOnConfirm: false
                    },
                    function(isComfirm){
                      if(isComfirm){
                        // 意見を削除
                        $.ajax({
                          type: "POST",
                          url: "//chienoma.net/delete_ka.php",
                          data: {
                            "val": val, // ka_idなはず
                            "token": token
                          }
                        })
                        .then(
                          function (data) {
                            // 1つめは通信成功時のコールバック
                            // var id = '#kansou' + val;
                            // $(id).hide();
                            // swal("意見を削除しました", "", "success");
                            location.reload();
                          },
                          function (data) {
                            // 2つめは通信失敗時のコールバック
                            console.log(data);
                            swal("エラーが発生しました。", "", "success");
                          }
                        );
                      }else{
                        // 入力値がない場合…何もしない
                      }
                    });
                  }else if(check_type == 'sa_delete'){
                    swal({
                      title: '募集を削除します',
                      text: '削除すると復元できません。削除してよろしいですか？',
                      type: "warning",
                      showCancelButton: true,
                      confirmButtonColor: '#DD6B55',
                      confirmButtonText: '削除する',
                      cancelButtonText: "キャンセル",
                      closeOnConfirm: false
                    },
                    function(isComfirm){
                      if(isComfirm){
                        // 意見募集を削除
                        $.ajax({
                          type: "POST",
                          url: "//chienoma.net/delete_sa.php",
                          data: {
                            "val": val, // sa_idなはず
                            "token": token
                          }
                        })
                        .then(
                          function (data) {
                            // 1つめは通信成功時のコールバック
                            window.location.href = "//chienoma.net/sakuhin";
                          },
                          function (data) {
                            // 2つめは通信失敗時のコールバック
                            swal("エラーが発生しました。", "", "success");
                          }
                        );
                      }else{
                        // 入力値がない場合…何もしない
                      }
                    });
                  }else if(check_type == 'sa_edit'){
                    $.ajax({
                      type: "POST",
                      url: "//chienoma.net/edit_sa.php",
                      data: {
                        "val": val, // sa_idなはず
                        "token": token
                      }
                    })
                    .then(
                      function (data) {
                        // 1つめは通信成功時のコールバック
                        window.location.href = "//chienoma.net/bosyu";
                      },
                      function (data) {
                        // 2つめは通信失敗時のコールバック
                        swal("エラーが発生しました。", "", "success");
                      }
                    );
                  }else{
                    swal("エラーが発生しました。", "", "success");
                  }
                }
              },
              function (data) {
                // 2つめは通信失敗時のコールバック
                console.log(data);
                swal("エラーが発生しました。", "", "success");
              }
            );  // end ajax check_pass
          }else{
            // 入力値がない場合…何もしない
          }
        });
      }
    },
    function (data) {
      // 2つめは通信失敗時のコールバック
      console.log(data);
      swal("エラーが発生しました。", "", "success");
    }
  );  // end ajax check_dele_edi_user
}); // end function

// show_sumbnail
$(document).ready( function(){
  //画像ファイルプレビュー表示のイベント追加 fileを選択時に発火するイベントを登録
  $('form').on('change', 'input[type="file"]', function(e) {
    var file = e.target.files[0],
        reader = new FileReader(),
        $preview = $(".preview");
        t = this;

    // 画像ファイル以外の場合は何もしない
    if(file.type.indexOf("image") < 0){
      return false;
    }

    // ファイル読み込みが完了した際のイベント登録
    reader.onload = (function(file) {
      return function(e) {
        //既存のプレビューを削除
        $preview.empty();
        // .prevewの領域の中にロードした画像を表示するimageタグを追加
        $preview.append($('<img>').attr({
                  src: e.target.result,
                  title: file.name
              }));
      };
    })(file);

    reader.readAsDataURL(file);
  });
});


// show_more
$(document).ready( function(){
  //liの個数を取得しておく
  var listContents = $(".more_list li").length;
  $(".more_list").each(function(){

    //最初に表示させるアイテムの数
    var Num = 10,
    gtNum = Num-1;

    //最初はmoreボタン表示にし、
    $(this).find('#more_btn').show();
    //10行目まで表示
    $(this).find("li:not(:lt("+Num+"))").hide();

    //liの個数よりNumが多い時
    if(listContents <= Num){
      $('#more_btn').hide();
    }
    //moreボタンがクリックされた時
    $('#more_btn').on('click', function() {
      //Numに+10ずつしていく = 10行ずつ追加する
      Num +=10;
      $(this).parent().find("li:lt("+Num+")").slideDown();
      //liの個数よりNumが多い時
      if(listContents <= Num){
        $('#more_btn').hide();
      }
    });
  });
});
