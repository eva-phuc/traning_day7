<!DOCTYPE html>
<html lang="ja">
<head>

<meta charset="UTF-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="ROBOTS" content="NOYDIR,NOODP" >
<meta name="keywords" content="" >
<meta name="description" content="" >
<?php echo \Asset::css('app/reset.css') ?>
<?php echo \Asset::css('app/common.css') ?>
<?php echo \Asset::css('app/style.css') ?>

<?php echo \Asset::js('jquery.min.js') ?>
<script type="text/javascript" async="" src="http://www.google-analytics.com/analytics.js"></script>
<script async="" src="//www.googletagmanager.com/gtm.js?id=GTM-57SGBZ"></script>

</head>
<body>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-57SGBZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-57SGBZ');</script>
<!-- End Google Tag Manager -->

    <header><p><?php echo $head_title ?></p></header>

    <?php echo $content; ?>

    <footer>
    <ul>
        <li><a class="a_moji" href="#">ログアウト</a></li>
        <li><a class="a_moji" href="#">特定商取引法に基づく表示</a></li>
        <li><a class="a_moji" href="#">利用規約</a></li>
        <li><a class="a_moji" href="#">お問い合わせ</a></li>
    </ul>
    </footer>

</body>
</html>
