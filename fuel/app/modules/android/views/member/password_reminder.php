<div>
    <p class="message_small" style="color:#333;">ご登録頂いているメールアドレスを入力して「送信」ボタンを押して下さい。</p>
    <form action="/android/member/user/password_reminder" method="post">
        <ul>
            <li>
                <p>メールアドレス</p>
                <input type="email" placeholder="your@email.com" name="mailaddr" value="">
            </li>
<?php if(!empty($messages)): ?>
            <li>
                <span style="color:red;"><?php echo is_array($messages) ? implode('<br/>',$messages) : $messages ?></span>
            </li>
<?php endif; ?>
            <li>
                <input type="submit" value="送信する" id="form_btn02" style="margin:0;">
            </li>
        </ul>
    </form>
    
</div>

