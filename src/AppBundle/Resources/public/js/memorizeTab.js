var MT = {
    init:function(tab){
        $('#'+tab).after('<i id="'+tab+'-loader" class="fa fa-circle-o-notch fa-spin fa-fw"></i>');

        $('#'+tab+' a').each(function(index){
           $(this).prop("disabled",true)
        });

        $.ajax({
            type: "POST",
            url: Routing.generate('app_ajax_memorizedTab'),
            data : { tab : tab }
        })
            .done(function (data) {
                var minOne = false;
                $('#'+tab+' a').each(function(index){
                    var val = $(this).attr('href');
                    if(val == data){
                        minOne = true;
                        $(this).addClass('active');
                        $(val).addClass('active');
                    }else{
                        $(this).removeClass('active');
                        $(val).removeClass('active');
                    }
                    $(this).prop("disabled",false)
                    $(this).click(function() {
                        $.ajax({
                            type: "POST",
                            url: Routing.generate('app_ajax_memorizeTab'),
                            data : { tab : tab, target : val }
                        })
                    });

                    $('#'+tab+'-loader').remove();
                });
                if(!minOne){
                    $('#'+tab+' a').first().addClass('active');
                    var val =  $('#'+tab+' a').first().attr('href');
                    $(val).addClass('active');
                }
            });
    }
}