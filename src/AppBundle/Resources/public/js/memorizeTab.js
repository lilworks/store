var MT = {
    init:function(tab){
        $('#'+tab+'-container').hide();
        $.ajax({
            type: "POST",
            url: Routing.generate('app_ajax_memorizedTab'),
            data : { tab : tab }
        })
            .done(function (data) {
                $('#'+tab+' a').each(function(index){
                    var val = $(this).attr('href');

                    if(val == data){
                        $(this).addClass('active');
                        $(val).addClass('active');
                    }else{
                        $(this).removeClass('active');
                        $(val).removeClass('active');
                    }
                    $('#'+tab+'-container').show();
                    $(this).click(function() {
                        $.ajax({
                            type: "POST",
                            url: Routing.generate('app_ajax_memorizeTab'),
                            data : { tab : tab, target : val }
                        })
                    });
                });
            });
    }
}