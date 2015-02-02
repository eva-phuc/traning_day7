<?php if(!empty($json_response)): ?>
<script type="text/javascript" >
<!--
function applicationNotice()
{
    <?php if('login' == \Request::main()->uri->segment(3)): ?>
    document.location = 'app-action://setLoginInfo(<?php echo urlencode($json_response) ?>)';
    <?php elseif('regist' == \Request::main()->uri->segment(3)): ?>
    document.location = 'app-action://returnRegistInfo(<?php echo urlencode($json_response) ?>)';
    <?php endif; ?>
    return false;
}
//-->
</script>
<?php endif; ?>