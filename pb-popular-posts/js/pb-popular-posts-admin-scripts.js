jQuery(document).ready(function($) {
    $('.pb-popular-del').click(function(e){
        
        e.preventDefault();
        if ( !confirm("Confirm removing") ) return false;

        var post = $(this).data('post'),
            parent = $(this).parent(),
            loader = parent.next(),
            li = $(this).closest('li');

        console.log(post);
        console.log(parent);
        console.log(loader);
        console.log(li);

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                security: pbPopulars.nonce,
                action: 'pb_del',
                postId: post
            },
            beforeSend: function(){
                parent.fadeOut(300, function(){
                    loader.fadeIn();
                });
            },
            success: function(res){
                loader.fadeOut(300, function(){
                    li.html(res);
                });
            },
            error: function(){
                alert("Error!");
            }
        })
    });

    $('#pb-popular-del-all').click(function(e){
        e.preventDefault();
        if ( !confirm("Confirm removing") ) return false;

        var $this = $(this),
            loader = $this.next(),
            parent = $this.parent(),
            list = parent.prev();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                security: pbPopulars.nonce,
                action: 'pb_del_all',
            },
            beforeSend: function(){
                $this.fadeOut(300, function(){
                    loader.fadeIn();
                });
            },
            success: function(res){
                loader.fadeOut(300, function(){
                    if (res === "List is cleared")
                    {
                        parent.html(res);
                        list.fadeOut();
                    }
                    else{
                        $this.fadeIn();
                        alert(res);
                    }
                });
            },
            error: function(){
                alert("Error!");
            }
        })
    });   
});