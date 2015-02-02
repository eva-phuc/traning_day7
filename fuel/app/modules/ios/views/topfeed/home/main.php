<?php echo Form::hidden('sort', \Input::param('sort')) ?>
<div id="wrap">
  <?php echo $itemList;?>
</div>

<!-- loading -->
<div id="loading" class="img_loading" style="display:none;">
  <img src="/img/common/img_loading30px_gray.GIF" alt="loading..." width="30" height="30">
</div>
<!-- loading -->