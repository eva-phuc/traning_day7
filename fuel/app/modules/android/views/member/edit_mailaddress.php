<div>
  <?php echo Form::open(array('action'=>'','method'=>'post')); ?>
<?php if(! empty($messages)): ?>
    <p class="p_error03"><?php echo implode('<br>',$messages) ?></p>
<?php endif; ?>
    <ul>
      <li>
        <p>現在のメールアドレス</p>
        <input type="text" value="<?php echo \Aucfan\Auth::instance()->get_user_info('mail_addr'); ?>">
      </li>
      <li>
        <p>新しいメールアドレス</p>
        <input type="text" name="mailaddr" value="" />
      </li>
      <li>
        <p>新しいメールアドレス再入力</p>
        <input type="text" name="mailaddr_cnf" value="" />
      </li>
      <li>
        <p>パスワード</p>
        <input type="password" name="password" value="" />
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
document.location = 'app-action://setAlertInfo(<?php echo urlencode(json_encode(array('title'=>\Config::get('user.mappings.alert_title.edit_mailaddress'),'messages'=>implode("\n",$messages)))) ?>)';
</script>
<?php endif; ?>


<div class="regist_box bd_01 bg_01 pd_01">
<ul class="regist_form">
<li>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">現在のメールアドレス</span></dt>
<dd class="span8"><?php echo \Aucfan\Auth::instance()->get_user_info('mail_addr'); ?></dd>
</dl>
</li>

<li<?php //if(is_array($messages) && !empty($messages['password'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">パスワード</span></dt>
<dd class="span8"><input type="password" name="password" value="" />
<?php /**if(is_array($messages) && !empty($messages['password'])): ?>
<span class="alert_txt"><?php echo $messages['password']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>

</li>

<li<?php //if(is_array($messages) && !empty($messages['mailaddr'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">新しいメールアドレス</span></dt>
<dd class="span8"><input type="text" name="mailaddr" value="" />
<?php /**if(is_array($messages) && !empty($messages['mailaddr'])): ?>
<span class="alert_txt"><?php echo $messages['mailaddr']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>
</li>

<li<?php //if(is_array($messages) && !empty($messages['mailaddr_cnf'])) echo ' class="control-group error"'; ?>>
<dl class="row-fluid">
<dt class="span4"><span class="pass_edit_tl">新しいメールアドレス（再入力）</span></dt>
<dd class="span8"><input type="text" name="mailaddr_cnf" value="" /><span class="txt_ex">確認のためにもう一度入力して下さい。</span>
<?php /**if(is_array($messages) && !empty($messages['mailaddr_cnf'])): ?>
<span class="alert_txt"><?php echo $messages['mailaddr_cnf']; ?></span>
<?php endif;**/ ?>
</dd>
</dl>
</li>
</ul>
</div>



<button type="submit" class="btn_action row_one01 w_set01"><span>メールアドレスを変更する</span></button>

<?php echo Form::close(); ?>

-->