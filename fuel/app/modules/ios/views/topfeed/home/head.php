  <script src="/assets/js/jquery.bottom-1.0.js"></script>
  <script type="text/javascript">
  
    // 画面最下部スクロールで次ページ商品読み込み
    var page_cnt = 1;
    $(function() {
        $(window).bottom({proximity: 0.05});
        $(window).on('bottom', function() {
            var obj = $(this);
            if (!obj.data('loading')) {
                obj.data('loading', true);
                $('#loading').css("display", "block");
                setTimeout(function(){ 
                    var sort = document.getElementById("form_sort").value;
                    page_cnt = Number(page_cnt+1);
                    $.ajax({
                        type:"GET",
                        url: "/ios/topfeed/home/getitems/?page="+page_cnt+"&sort="+sort,
                        cache: false,
                        success: function(rtn){
                        
                            if(rtn == 'error') autoJump();
                            
                            if(rtn != 'none') {
                                $('#wrap').append(rtn);
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
    
    function autoJump(){
        location.href="/ios/error/home/index/error00/";
    }
    
  </script>
  