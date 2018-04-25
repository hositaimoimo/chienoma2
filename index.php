
<?php
if (!isset($_SESSION)) {
  session_start();
}

// ユーザーチェックはfoot.phpのuser_check.js→user_check.phpで行っている
// ユーザーがどこかのページを開いた段階で、$_SESSION['random_id']に値が格納される

header ("Content-type: text/html; charset=utf-8");

require_once ( __DIR__ . '/functions.php');
require_once ( __DIR__ . '/config.php');

// DBに接続
$dbh = get_db_connect ();

// リクエストURLのスラッシュを除去
$request_url = ltrim(html_escape($_SERVER['REQUEST_URI']), '/');
$_SESSION['request_url'] = $request_url;

// CSRF対策
if(empty($_SESSION['token'])){
  $token = set_token();
}else{
  $token = $_SESSION['token'];
}

// ツイート用
$_SESSION['content'] = "ChieNoMa";

// リクエストURLによって表示内容を調節
switch ($request_url) {
	case '':
	case 'favicon.ico':
		///////////////////////////////////////////////////
		// トップページ
		///////////////////////////////////////////////////
		$_SESSION['title'] = '気軽に意見が聞ける場所';
		require_once ( __DIR__ . '/head.php');
		require_once ( __DIR__ . '/v_top.php');
		break;

	case 'bosyu':
		///////////////////////////////////////////////////
		// 感想募集ページ
		///////////////////////////////////////////////////
    if($_SESSION['is_edit_mode'] == TRUE){
      $_SESSION['title'] = '募集内容の修正';
    }else{
      $_SESSION['title'] = '意見を募集する';
    }
		require_once ( __DIR__ . '/head.php');
    require_once ( __DIR__ . '/v_bosyu.php');
		break;


	case 'sakuhin':
		///////////////////////////////////////////////////
		// 作品一覧
		///////////////////////////////////////////////////
		$_SESSION['title'] = '意見募集中リスト';
    $sakuhin_list = get_sakuhin_list($dbh);
		require_once ( __DIR__ . '/head.php');
    require_once ( __DIR__ . '/v_sakuhin_list.php');
		break;

  case 'privacy':
    ///////////////////////////////////////////////////
    // プライバシーポリシー
    ///////////////////////////////////////////////////
    $_SESSION['title'] = 'プライバシーポリシー';
    require_once ( __DIR__ . '/head.php');
    require_once ( __DIR__ . '/v_privacy.php');
    break;

  case 'tos':
    ///////////////////////////////////////////////////
    // 利用規約
    ///////////////////////////////////////////////////
    $_SESSION['title'] = '利用規約';
    require_once ( __DIR__ . '/head.php');
    require_once ( __DIR__ . '/v_tos.php');
    break;


  default:
		if(preg_match('|^sakuhin+[/]{1}[s]{1}[0-9]+$|', $request_url)){
			list($url1, $url2) = explode("/s", $request_url);
			// $url2によって表示内容を切り替え
			// $url2と一致する作品IDがなければ404

      // 作品を取得
      $sakuhin = get_sakuhin($dbh, $url2);
      // 感想を取得
      $kansou_list = get_kansou_list($dbh, $sakuhin['sa_id']);
			if($sakuhin == NULL || $url1 != 'sakuhin') {
				///////////////////////////////////////////////////
				// 404 not found
				///////////////////////////////////////////////////
				$_SESSION['title'] = 'お探しのページが見つかりませんでした';
				require_once ( __DIR__ . '/head.php');
				require_once ( __DIR__ . '/404.php');
			}else{
				///////////////////////////////////////////////////
				// 各作品ページ
				///////////////////////////////////////////////////

				// ↓DBから作品名を取得する
        $sa_title = $sakuhin['sa_title'];
				$_SESSION['title'] = $sa_title;
        // ツイート用
        $_SESSION['content'] = $sakuhin['sa_content'];
        require_once ( __DIR__ . '/head.php');
        require_once ( __DIR__ . '/v_sakuhin.php');
			}
		}else{
			///////////////////////////////////////////////////
			// 404 not found
			///////////////////////////////////////////////////
			$_SESSION['title'] = 'お探しのページが見つかりませんでした';
			require_once ( __DIR__ . '/head.php');
			require_once ( __DIR__ . '/404.php');

		}
		break;
}
require_once ( __DIR__ . '/footer.php');
