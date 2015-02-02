<?php echo Form::open(array('action'=>'','method'=>'post','name'=>'frm')) ?>
    <div>メール通知　<?php echo Form::radio('mail_alert',0,(empty($param['mail_alert']) ?:null),array('onclick'=>'document.frm.submit();'))?>ON　<?php echo Form::radio('mail_alert',1,(1==$param['mail_alert'] ?:null),array('onclick'=>'document.frm.submit();'))?>OFF</div>
<?php echo Form::close() ?>
