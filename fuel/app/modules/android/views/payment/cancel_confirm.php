<div>
<?php if(true === (bool)$get_form_error()): ?>
    <?php foreach($get_form_error() as $error): ?>
    <p class="padding_space top10px line_height" style="color:red"><?php echo $error ?></p>
    <?php endforeach; ?>
<?php endif; ?>
    <section class="kaiyaku">
        <h1 class="h1_gray">ご契約内容</h1>
        <p class="padding_space top10px gray line_height"><?php echo \Config::get('p_credit.forms.service_label') ?><br>月額<?php echo \Config::get('p_credit.forms.service_fee_extax') ?>円（税抜）</p>
    </section>
    <!-- /kaiyaku -->
    <section class="kaiyaku top20px">
        <h1 class="h1_gray">お支払い方法</h1>
        <p class="padding_space top10px gray line_height"><?php echo \Config::get('p_credit.forms.payment_label.'.$get_user_info('alermo_payment_method')) ?><br><?php echo $get_card_number() ?></p>
    </section>
    <!-- /kaiyaku -->
    <p class="message top30px">ご利用期間とお支払い方法をご確認のうえ、<br>「解約する」ボタンを押して下さい。</p>
    <p><a href="/android/payer_menu.html" class="btn02">戻る</a></p>
    <p><a href="javascript.void(0);" class="btn01" onClick="document.frm1.submit();return false;">解約する</a></p>

<form action="/android/payment/cancel/execute" method="post" name="frm1">
</form>
</div>
