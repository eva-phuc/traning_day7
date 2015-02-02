<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="ROBOTS" content="NOYDIR,NOODP" >
  <meta name="keywords" content="" >
  <meta name="description" content="" >
  <title></title>
  <link rel="stylesheet" href="/assets/css/app/reset.css" media="all">
  <link rel="stylesheet" href="/assets/css/app/common.css" media="all">
  <link rel="stylesheet" href="/assets/css/app/style.css" media="all">
  <script src="/assets/js/jquery.js"></script>
  <?php if(isset($head)) echo $head; ?>
</head>

<body<?php echo (!empty($json_response)) ? ' onload="applicationNotice();"' : '' ?>>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-57SGBZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-57SGBZ');</script>
<!-- End Google Tag Manager -->

<?php if(isset($content)) echo $content; ?>

</body>
 
</html>