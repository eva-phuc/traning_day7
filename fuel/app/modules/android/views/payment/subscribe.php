<div>
    <form action="/android/payment/credit/subscribe" method="post" id="cc_form">

<?php if(true === (bool)($error = $get_form_error())): ?>
    <?php foreach($error as $err): ?>
        <p class="p_error03"><?php echo $err ?></p>
    <?php endforeach; ?>
<?php endif; ?>

        <p class="p14px message">ご登録されるクレジットカードは<br>株式会社オークファンが提供するサービスで<br>共通してご利用頂けます。</p>
        <ul>
            <li><p>クレジットカード番号</p>
                <?php echo \Form::input('cardno', \Input::post('cardno')) ?>
                <img class="top10px" src="/img/common/img_credit03.png" alt="クレジットカード" width="100%">
            </li>
            <li>
                <p>有効期限</p>
                <?php echo \Form::select('cardmonth',\Input::post('cardmonth'),$get_expiration_month(),array('class'=>'cardmonth')) ?>
                &nbsp;/&nbsp;
                <?php echo \Form::select('cardyear',\Input::post('cardyear'),$get_expiration_year(),array('class'=>'cardyear')) ?>
            </li>
            <li class="cf">
                <p>セキュリティコード</p>
                <p class="p_small">クレジットカード裏面の3桁あるいは4桁の番号</p>
                <?php echo \Form::input('security_code',null,array('class'=>'security_code')) ?>
                <img class="top03px" src="/img/common/img_credit02.png" alt="セキュリティコード" width="140" height="30">
            </li>
            <li>
                <input type="checkbox" value="1" name="agree">&nbsp;&nbsp;<a class="a_moji02" href="#">個人情報の取り扱い</a>について同意する
            </li>
            <li>
                <input type="submit" value="登録する" id="form_btn02">
            </li>
        </ul>
    </form>
</div>
<script type="text/javascript">
$(function() {
    $('#form_btn02').attr('disabled','disabled');
    $('#cc_form').submit(function() {
        if(1 != $('#cc_form [name=agree]:checked').val())
            return false;

        return true;
    });

    $('#cc_form [name=agree]').change(function() {
        if ($(this).is(':checked')) 
            $('#form_btn02').removeAttr('disabled');
        else
            $('#form_btn02').attr('disabled','disabled');
    }); 
});
</script>
