<div>
    <form action="/android/payment/credit/execute_subscribe" method="post">
        <?php echo \Form::hidden('post_info',urlencode(serialize(\Input::post()))) ?>
        <ul>
            <li>
                <p class="pink p13px">クレジットカード情報を以下の内容で登録します</p>
            </li>
            <li>
                <p>クレジットカード番号</p>
                <p class="p19px_gray"><?php echo $create_masked_cardno(\Input::post('cardno')) ?></p>
            </li>
            <li>
                <p>有効期限</p>
                <p class="p19px_gray"><?php echo \Input::post('cardmonth')?>&nbsp;/&nbsp;<?php echo \Input::post('cardyear') ?></p>
            </li>
            <li>
                <p>ご契約内容</p>
                <p class="p19px_gray"><?php echo \Config::get('p_credit.forms.service_label') ?>&nbsp;月額<?php echo \Config::get('p_credit.forms.service_fee') ?>円(税抜)</p>
            </li>
            <li>
                <p><a class="a_moji02" href="#"><?php echo \Config::get('p_credit.forms.service_label') ?>利用規約</a></p>
            </li>
            <li>
                <input type="submit" value="規約に同意して登録する" id="form_btn02">
            </li>
        </ul>
    </form>
</div>
