<div>
    <p class="message_big_pink">解約するなら月末がお得!</p>
    <p class="message">月の途中で解約しても月末まで<br>月額料金<?php echo \Config::get('p_credit.forms.service_fee_extax') ?>円（税抜）は変わりません。</p>
    <p><a href="javascript:history.back();" class="btn02">月末まで利用する</a></p>
    <section class="kaiyaku">
        <h1 class="h1_gray top20px">次の理由で解約をご検討ではありませんか？</h1>
        <dl>
            <dt>■スマートフォンを機種変更する</dt><?php echo $get_user_info()?>
            <dd>
                <p class="p_box">ご利用中の決済手段：<?php echo \Config::get('p_credit.forms.payment_label.'.$get_user_info('alermo_payment_method')) ?>決済</p>
                <p><?php echo \Config::get('p_credit.forms.payment_label.'.$get_user_info('alermo_payment_method')) ?>決済のお客様は、機種変更後も今までと変わらずプレミアムサービスをご利用いただけます。<br>特にお手附は必要ございません。</p>
            </dd>
            <dt>■PCで使いたい</dt>
            <dd>
                <p>PCでもALERMOも利用可能になれます。<br>同じメールアドレスやパスワードでプレミアムサービスをご利用いただけます。</p>
                <p class="p_box">ご利用料金について申込月、または月の途中で解約されても、月額料金<?php echo \Config::get('p_credit.forms.service_fee_extax') ?>円（税抜）が発生します。</p>
            </dd>
        </dl>
    </section>
    <!-- /kaiyaku -->
    <p><a href="javascript:history.back();" class="btn02">戻る</a></p>
    <p><a href="/android/payment/cancel/lp" class="btn01">次へ</a></p>
</div>
