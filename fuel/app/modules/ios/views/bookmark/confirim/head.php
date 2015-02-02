<?php if(!empty($json_response)): ?>
  <script type="text/javascript" >
  <!--
  function applicationNotice()
  {
      document.location = 'app-action://success(<?php echo urlencode($json_response) ?>)';
      return false;
  }
  //-->
  </script>
<?php endif; ?>