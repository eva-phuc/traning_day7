<div>
    <p class="p14px message">クレジットカード登録情報</p>
    <section>
        <h1 class="h1_gray">登録されているクレジットカード</h1>
        <p class="message top10px gray line_height"><?php echo $get_card_number() ?></p>
        <p class="message top10px gray line_height">有効期限 <?php echo $get_card_expire() ?></p>
    </section>
    <section class="top20px">
        <h1 class="h1_gray">新しく登録されるクレジットカード</h1>

<?php if(true === (bool)($error = $get_form_error())): ?>
    <?php foreach($error as $err): ?>
        <p class="p_error03"><?php echo $err ?></p>
    <?php endforeach; ?>
<?php elseif(! empty($done_message)): ?>
        <p class="p_error03" style="color:blue;"><?php echo $done_message ?></p>
<?php endif; ?>
        <form action="/android/payment/cardchange" method="post" id="cc_form">
            <ul>
                <li><p>クレジットカード番号</p>
                <?php echo \Form::input('cardno', \Input::post('cardno')) ?>
                </li>
                <li>
                    <p>有効期限</p>
                    <?php echo \Form::select('cardmonth',\Input::post('cardmonth'),$get_expiration_month(),array('class'=>'cardmonth')) ?>
                    &nbsp;/&nbsp;
                    <?php echo \Form::select('cardyear',\Input::post('cardyear'),$get_expiration_year(),array('class'=>'cardyear')) ?>
                </li>
                <li>
                <?php //echo \Form::input('security_code',null,array('class'=>'security_code')) ?>
                <?php echo \Form::hidden('security_code',111) ?>
                    <input type="submit" value="カード情報を変更する" id="form_btn02">
                </li>
            </ul>
        </form>
    </section>
</div>

