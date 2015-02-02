<div>
  <p class="message_small" style="color:#333;">以下の内容で宜しいでしょうか?</p>
  <form action="/ios/member/regist/execute" method="post" name="regist_frm" id="regist_frm">
    <?php foreach($posted as $key => $value) echo Form::hidden($key,$value); ?>
    <ul>
      <li>
        <p>ニックネーム</p>
        <p class="p19px_gray"><?php echo $posted['nickname']?></p>
      </li>
      <li>
        <p>メールアドレス</p>
        <p class="p19px_gray"><?php echo $posted['mailaddr']?></p>
      </li>
      <li>
        <p>パスワード</p>
        <p class="p19px_gray">***********</p>
        <p class="message_small" style="padding:0; margin:0 0 0; color:#999; font-size:11px;">※セキュリティ確保のため、表示しません。</p>
      </li>
      <li>
        <input type="submit" value="この内容で確定する" id="form_btn03">
        <input type="submit" value="戻る" onclick="registBack();" id="form_btn02">
      </li>
    </ul>
  </form>
</div>

<script type="text/javascript">
<!--
function registBack()
{
    document.regist_frm.action = '/ios/member/regist';
    var input = document.createElement("input");
    input.setAttribute("type","hidden");
    input.setAttribute("name","is_back");
    input.setAttribute("value","1");

    document.getElementById("regist_frm").appendChild(input);
    document.regist_frm.submit();
    return true;
}
//-->
</script>
