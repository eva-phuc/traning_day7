  <script src="/assets/js/jquery.bottom-1.0.js"></script>
  <script type="text/javascript">
    
    // スクロールページネーション
    var page_cnt = 1;
    $(function() {
        $(window).bottom({proximity: 0.05});
        $(window).on('bottom', function() {
            var obj = $(this);
            if (!obj.data('loading')) {
                obj.data('loading', true);
                $('#loading').css("display", "block");
                setTimeout(function(){ 
                    var _disfa = $("#form__disfa").val();
                    var sort = $("#form_sort").val();
                    page_cnt = Number(page_cnt+1);
                    $.ajax({
                        type:"GET",
                        url: "/ios/myfeed/home/getitems/?page="+page_cnt+"&sort="+sort+"&_disfa="+_disfa,
                        cache: false,
                        success: function(rtn){
                        
                            if(rtn == 'error') autoJump();
                            
                            if(rtn != 'none') {
                                $("#wrap").append(rtn);
                                $('#loading').toggle();
                                obj.data('loading', false);
                            } else {
                                $('#loading').remove();
                            }
                        }
                    });
                },1000);
            }
            
        });
        
    });


    // アラート変更
    function changeButton(id)
    {
        var url = '/ios/myfeed/bookmark/change/';
        params = getParams(id);
        
        $.ajax(
        {
            type:"POST",
            url:url,
            cache:false,
            data:params,
            success:function(rtn)
            {
                if(rtn == 'error') autoJump();
                
                $("#alert_on_"+id).toggle();
                $("#alert_off_"+id).toggle();
                if(params["alert"] == "on"){
                    $("#form_alert_"+id).val("off");
                }else{
                    $("#form_alert_"+id).val("on");
                }
            },
            error:function(rtn)
            {
                autoJump();
            }
        });
    }


    // ブックマーク解除
    function deleteButton(id, delete_flag)
    {
        var url = '/ios/myfeed/bookmark/delete/';
        params = getParams(id);
        params['delete_flag'] = delete_flag;
        
        $.ajax(
        {
            type:"POST",
            url:url,
            cache:false,
            data:params,
            success:function(rtn)
            {
                if(rtn == 'error') autoJump();
                
                $("#bookmark_on_"+id).toggle();
                $("#bookmark_off_"+id).toggle();
                
            },
            error:function(rtn)
            {
                autoJump();
            }
        });
    }


    function getParams(id){
        var _disfa = $("#form__disfa").val();
        var alert = $("#form_alert_"+id).val();
        params = {bookmark_id:id, disfa:_disfa, alert:alert};
        return params;
    }
    function autoJump(){
        location.href="/ios/error/home/index/error00/";
    }

</script>
