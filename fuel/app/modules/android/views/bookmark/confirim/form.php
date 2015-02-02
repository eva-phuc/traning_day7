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
         <?php $opdf = (empty($inputParams['option'][$key])) ? 'selected' : ''; ?>
         <select name="<?php echo 'option['.$key.']';?>">
           <option value="" <?php echo $opdf;?> disabled><?php echo $value ?></option>
           <?php foreach($option_value[$key] as $opKey => $opValue): ?>
             <?php $opsd = (! empty($inputParams['option'][$key]) && $inputParams['option'][$key] == $opKey) ? 'selected' : ''; ?>
             <option value="<?php echo $opKey;?>" <?php echo $opsd;?>><?php echo $opValue;?></option>
           <?php endforeach;?>
         </select>
       </li>
       <?php endforeach;?>
     <?php endif;?>
     <li>
       <select name="alert">
         <?php $aldf = (empty($inputParams['alert'])) ? 'selected' : ''; ?>
         <option value="" <?php echo $aldf;?> disabled>通知の有無</option>
         <?php foreach($alert as $key => $value): ?>
           <?php $alsd = (! empty($inputParams['alert']) && $inputParams['alert'] == $key) ? 'selected' : ''; ?>
           <option value="<?php echo $key;?>" <?php echo $alsd;?>><?php echo $value;?></option>
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
