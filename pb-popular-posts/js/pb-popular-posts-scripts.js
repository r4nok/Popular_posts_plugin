jQuery(document).ready(function($) {
    $('.pb-popular-link a').click(function(e){
        var str_action = $(this).data('action');
        $.ajax({
            type: 'POST',
            url: pbPopulars.url,
            data: {
                security: pbPopulars.nonce,
                action: 'pb_' + str_action,
                postId: pbPopulars.postId
            },
            beforeSend: function(){
                $('.pb-popular-link a').fadeOut(300, function(){
                    $('.pb-popular-link .pb-popular-hidden').fadeIn();
                });
            },
            success: function(res){
                $('.pb-popular-link .pb-popular-hidden').fadeOut(300, function(){
                    if (str_action == 'del'){
                        $('.pb-popular-link').html(res);
                        $('.widget_pb-popular-widget').find('li.cat-item-' + pbPopulars.postId).remove();
                    }
                    if (str_action == 'add'){

                        var resArray = $.parseJSON(res);

                        $('.pb-popular-link').html(resArray['message']);
                        $('.widget_pb-popular-widget ul').prepend(resArray['data']);
                    }
                });
            },
            error: function(){
                alert("Error!");
            }
        })
        e.preventDefault(); // отменяем дефолтное поведение ссылки
    });
});