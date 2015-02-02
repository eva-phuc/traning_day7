<div>
  <?php echo Form::open(array('action'=>'','method'=>'post')); ?>
<?php if(! empty($messages)): ?>
    <p class="p_error03"><?php echo implode('<br>',$messages) ?></p>
<?php endif; ?>

    <ul>
      <li>
        <p>現在のパスワード</p>
        <input type="password" name="password" value="" />
      </li>
      <li>
        <p>新しいパスワード</p>
        <input type="password" name="new_password" value="" />
      </li>
      <li>
        <p>新しいパスワード（再入力）</p>
        <input type="password" name="new_password_cnf" value="" />
      </li>
      <li>
        <input type="submit" value="変更する" id="form_btn02">
      </li>
    </ul>
  <?php echo Form::close(); ?>
</div>




<!--

<?php echo Form::open(array('action'=>'','method'=>'post')); ?>

<?php if(! empty($messages)): ?>
<script type="text/javascript">
document.location = 'app-action://setAlertInfo(<?php echo urlencode(json_encode(array('title'=>\Config::get('user.mappings.alert_title.edit_passwd'),'messages'=>implode("\n",$messages)))) ?>)';
</script>
<?php endif; ?>

<div class="regist_box bd_01 bg_01 pd_01">
<ul class="regist_form">
<li<?php //if(!empty($messages['password'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">現在のパスワード</span></dt>
<dd class="span8"><input type="password" name="password" value="" />
<?php /**if(!empty($messages['password'])): ?>
<span class="alert_txt"><?php echo $messages['password']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>
</li>

<li<?php //if(!empty($messages['new_password'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">新しいパスワード</span></dt>
<dd class="span8"><input type="password" name="new_password" value="" /><span class="txt_ex">半角英数字-_.の組み合わせ8～16文字で入力して下さい。</span>
<?php /**if(!empty($messages['new_password'])): ?>
<span class="alert_txt"><?php echo $messages['new_password']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>
</li>

<li<?php //if(!empty($messages['new_password_cnf'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">新しいパスワード（再入力）</span></dt>
<dd class="span8"><input type="password" name="new_password_cnf" value="" /><span class="txt_ex">確認のためにもう一度入力して下さい。</span>
<?php /**if(!empty($messages['new_password_cnf'])): ?>
<span class="alert_txt"><?php echo $messages['new_password_cnf']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>
</li>
</ul>
</div>



<button type="submit" class="btn_action row_one01 w_set01"><span>パスワード情報を変更する</span></button>

<?php echo Form::close(); ?>

-->