<!DOCTYPE html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <title>ALERMO</title>
  <link rel="stylesheet" href="<?php echo Uri::base(false);?>assets/css/app/reset.css" media="all">
  <link rel="stylesheet" href="<?php echo Uri::base(false);?>assets/css/app/common.css" media="all">
  <link rel="stylesheet" href="<?php echo Uri::base(false);?>assets/css/app/style.css" media="all">
  <script src="<?php echo Uri::base(false);?>assets/js/jquery.js"></script>
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


<div class="clip">
  <?php if (isset($msg)): ?>
    <p class="p_error02"><?php echo $msg;?></p>
  <?php endif;?>
  <section class="clip_img">
    <div id="clip_img_youso">
      <?php if (isset($img_url)): ?>
        <div>
          <img src="<?php echo $img_url;?>" alt="">
        </div>
      <?php endif;?>
    </div>
    <ul class="cf">
      <li class="clip_img_li01"><?php echo $site_name;?></li>
      <li class="clip_img_li02">&yen;<?php echo number_format($price);?></li>
    </ul>
  </section>
  <?php echo Form::open('/ios/bookmark/confirm/index');?>
   <ul>
     <?php if(isset($option_name)): ?>
       <?php foreach($option_name as $key => $value): ?>
       <li>
         <select name="<?php echo 'option['.$key.']';?>">
           <option value="" selected disabled><?php echo $value ?></option>
           <?php foreach($option_value[$key] as $opKey => $opValue): ?>
             <option value="<?php echo $opKey;?>"><?php echo $opValue;?></option>
           <?php endforeach;?>
         </select>
       </li>
       <?php endforeach;?>
     <?php endif;?>
     <li>
       <select name="alert">
         <option value="" selected disabled>通知の有無</option>
         <?php foreach($alert as $key => $value): ?>
           <option value="<?php echo $key;?>"><?php echo $value;?></option>
         <?php endforeach;?>
       </select?
     </li>
     <li>
      <input type="submit" value="クリップする" id="form_btn">
     </li>
   </ul>
   <?php echo Form::hidden('item_cashe', $item_cashe); ?>
   <?php echo Form::hidden('url', $url); ?>
   <?php echo Form::hidden('_disfa', $_disfa); ?>
  <?php echo Form::close();?>
</div>

</body>
</html>