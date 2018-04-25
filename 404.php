<?php

header("HTTP/1.1 404 Not Found");

require_once ( __DIR__ . '/config.php');

echo '<h2>お探しのページが見つかりませんでした</h2>';
echo '<p>ページが削除されたか非公開にされた、もしくはURLが間違っている可能性があります。</p>';
echo '<p><a href='.SITE_URL.'>TOPへ戻る</a></p>';
