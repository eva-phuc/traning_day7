<div>

  <?php echo Form::open('/android/inquiry/home/confirm') ?>
  
  <?php if (isset($error_msg)): ?>
    <?php foreach ($error_msg as $error): ?>
      <p class="p_error03"><?php echo $error ?></p>
    <?php endforeach ?>
  <?php endif;?>

  <ul>
    <li>
      <p>お問い合わせ項目</p>
      <?php echo Form::select('query_param', $query_param, $query_list);?>
    </li>
    <li>
      <p>ご連絡先</p>
      <?php echo Form::input('mail_addr', $mail_addr, array('placeholder' => 'your@email.com'));?>
    </li>
    <li>
      <p>お問い合わせ内容</p>
       <?php echo Form::textarea('body', \Input::post('body'), array('class' => 'text_space')); ?>
    </li>
    <li class="p_small">※メールアドレスが間違っていたり、ドメイン指定受信していると返答が届きませんのでご注意ください。</li>
    <li>
      <input type="submit" value="送信" id="form_btn02">
    </li>
  </ul>
  <?php echo Form::hidden('_disfa', $_disfa) ?>
  <?php echo Form::close() ?>
  
</div>
