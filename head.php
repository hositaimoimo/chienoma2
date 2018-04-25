<!DOCTYPE html>
<html lang="ja">
<head>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta property="og:title"       content="<?php echo $_SESSION['title']; ?>">
<meta property="og:description" content="<?php echo $_SESSION['content']; ?>">
<meta property="og:url"         content="<?php echo SITE_URL.'/'.$request_url ?>">
<meta property="og:image" content= "<?php echo SITE_URL. '/images/mascot.png' ?>">
<meta property="og:type"        content="website">
<meta name="twitter:card"       content="summary">

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<link rel="stylesheet" href="//chienoma.net/honoka/css/bootstrap.min.css"><!-- Honoka -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="//chienoma.net/style.css" media='all'>
<link rel="icon" href="<?php echo SITE_URL. '/images/favicon.ico' ?>">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<title><?php echo $_SESSION['title']; ?>｜ChieNoMa</title>

</head>
<body>
<?php require_once ( __DIR__ . '/header.php');
