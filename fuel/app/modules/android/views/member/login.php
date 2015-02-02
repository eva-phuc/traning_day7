<div>
  <h1 class="img_logo"><img src="/img/common/img_logo.png" alt="alermo"></h1>
  <form action="/android/member/login" method="post">
<?php if(! empty($error_messages)):?>
    <p class="p_error03"><?php echo implode('</p><p class="p_error03">',$error_messages) ?></p>
<?php endif; ?>
    <ul>
      <li>
        <p>メールアドレス</p>
        <input type="text" placeholder="your@email.com" name="account" value="">
      </li>
      <li>
        <p>パスワード入力</p>
        <input type="password" name="password" value="">
      </li>
      <li>
        <input type="submit" value="ログイン" id="form_btn02">
      </li>
      <li>
        <p class="p_center top20px"><a class="a_moji" href="#">※パスワードを忘れた方はこちら</a></p>
      </li>
    </ul>
  </form>
</div>