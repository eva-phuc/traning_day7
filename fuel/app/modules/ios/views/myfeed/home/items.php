<?php foreach ($items as $key => $value): ?>
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
        <?php if(! empty($value['option_values'])): ?>
          <?php foreach ($value['option_values'] as $optionValue): ?>
            <p class="p_color_size"><?php echo $optionValue['name'];?>&nbsp;/&nbsp;<?php echo $optionValue['value'];?></p>
          <?php endforeach;?>
        <?php endif;?>
        <ul class="cf">
          <li class="contents_clip_info_li01"><?php echo $value['shop_name'];?></li>
          <?php if ($value['alert'] == 'on'): ?>
            <?php echo Form::hidden('alert_'.$value['bookmark_id'], 'off') ?>
            <li id="alert_on_<?php echo $value['bookmark_id'];?>"><input type="image" src="/img/mypage/img_alert_on.png" alt="アラート機能" width="23" height="27" onclick="changeButton('<?php echo $value['bookmark_id'];?>'); return false;"></li>
            <li id="alert_off_<?php echo $value['bookmark_id'];?>" style="display:none;"><input type="image" src="/img/mypage/img_alert_off.png" alt="アラート機能" width="23" height="27" onclick="changeButton('<?php echo $value['bookmark_id'];?>'); return false;"></li>
          <?php else:?>
            <?php echo Form::hidden('alert_'.$value['bookmark_id'], 'on') ?>
            <li id="alert_on_<?php echo $value['bookmark_id'];?>" style="display:none;"><input type="image" src="/img/mypage/img_alert_on.png" alt="アラート機能" width="23" height="27" onclick="changeButton('<?php echo $value['bookmark_id'];?>'); return false;"></li>
            <li id="alert_off_<?php echo $value['bookmark_id'];?>"><input type="image" src="/img/mypage/img_alert_off.png" alt="アラート機能" width="23" height="27" onclick="changeButton('<?php echo $value['bookmark_id'];?>'); return false;"></li>
          <?php endif;?>
          <li id="bookmark_on_<?php echo $value['bookmark_id'];?>" class="contents_clip_info_li02"><input type="image" src="/img/mypage/img_clip_on.png" alt="クリップ" width="27" height="27" onclick="deleteButton('<?php echo $value['bookmark_id'];?>', 'on'); return false;"><?php echo $value['bookmark_count'];?></li>
          <li id="bookmark_off_<?php echo $value['bookmark_id'];?>" class="contents_clip_info_li02" style="display:none;"><input type="image" src="/img/mypage/img_clip_off.png" alt="クリップ" width="27" height="27" onclick="deleteButton('<?php echo $value['bookmark_id'];?>', 'off'); return false;"><?php echo $value['bookmark_count'];?></li>
        </ul>
      </div>
      <!-- contents_clip_info -->
    </section>
    <!-- contents_clip -->
    </a>
<?php endforeach;?>
