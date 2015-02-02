<div>
  <form action="/android/member/regist" method="post">
<?php if(! empty($errors)): ?>
    <p class="p_error03"><?php echo implode('<br>',$errors) ?></p>
<?php endif; ?>
    
    <?php echo Form::hidden('info_mail_is_permitted','D'); ?>
    <ul>
      <li><p>ニックネーム</p>
        <input type="text" name="nickname" value="<?php echo !empty($posted['nickname']) ? $posted['nickname'] : '' ?>"/>
      </li>
      <li>
        <p>メールアドレス</p>
        <input type="text" placeholder="your@email.com" name="mailaddr" value="<?php echo !empty($posted['mailaddr']) ? $posted['mailaddr'] : '' ?>"/>
      </li>
      <li>
        <p>メールアドレス再入力</p>
        <input type="text"placeholder="your@email.com" name="mailaddr_cnf" value="<?php echo !empty($posted['mailaddr_cnf']) ? $posted['mailaddr_cnf'] : '' ?>"/>
      </li>
      <li>
        <p>パスワード</p>
        <input type="password" name="password" value=""/>
      </li>
      <li>
        <p>パスワード再入力</p>
        <input type="password" name="password_cnf" value=""/>
      </li>
      <li>
        <p class="message_small" style="line-height:1.3;">ALERMOの<a class="a_moji" href="<?php echo Uri::base(false);?>terms.html">利用規約</a>および<a class="a_moji" href="<?php echo Uri::base(false);?>privacy.html">個人情報保護方針</a>をお読みいただき、同意される方のみ「同意して確認画面に進む」ボタンを押してください。</p>
        <input type="submit" value="同意して確認画面に進む" id="form_btn02" style="margin:0;">
      </li>
    </ul>
    <p class="message_small" style="text-align:center;">すでに登録されている方は<a class="a_moji" href="/android/member/login">コチラからログイン</a>
  </form>
</div>
