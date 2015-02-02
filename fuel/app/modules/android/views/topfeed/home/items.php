<?php foreach ($items as $value): ?>
  <a href="<?php echo $value['url'];?>">
  <section class="contents_clip">
    <img class="img_photo" src="<?php echo $value['img_url'];?>" alt="">
    <?php if(! empty($value['sale_price'])): ?>
      <img class="img_sale" src="/img/mypage/img_mp_sale.png" alt="sale" width="45" height="45">
    <?php endif;?>
    <div class="contents_clip_info">
      <h1 class="p_info_name"><?php echo $value['item_name'];?></h1>
      <p class="p_info_price">&yen;<?php echo number_format($value['price']);?>
        <?php if(! empty($value['sale_price'])): ?>
          <span class="p_info_price01">></span> <span class="p_info_price02">&yen;<?php echo number_format($value['sale_price']);?></span>
        <?php endif;?>
      </p>
      <ul class="cf">
        <li class="contents_clip_info_li01"><?php echo $value['shop_name'];?></li>
        <li class="contents_clip_info_li03"><?php echo $value['bookmark_count'];?></li>
      </ul>
    </div>
    <!-- contents_clip_info -->
  </section>
  <!-- contents_clip -->
  </a>
<?php endforeach;?>