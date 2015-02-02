<!DOCTYPE html>
<html lang="ja">
<head>

<meta charset="UTF-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="ROBOTS" content="NOYDIR,NOODP" >
<meta name="keywords" content="" >
<meta name="description" content="" >

<?php if(!empty($json_response)): ?>
<script type="text/javascript" >
<!--
function applicationNotice()
{
    <?php if('login' == \Request::main()->uri->segment(3)): ?>
    document.location = 'app-action://setLoginInfo(<?php echo urlencode($json_response) ?>)';
    <?php elseif('regist' == \Request::main()->uri->segment(3)): ?>
    document.location = 'app-action://returnRegistInfo(<?php echo urlencode($json_response) ?>)';
    <?php endif; ?>
    return false;
}
//-->
</script>
<?php endif; ?>

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

    <?php echo $content; ?>
</body>
</html>
