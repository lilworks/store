const Routing = require('./Routing.js');

var RE = {
    init: function (route) {
        RE.route = route;

        $('input:checkbox:checked').each(function(){
            $(this).prop('checked',false);
        });

        $( "#rowsEditor" ).click(function() {
            if($(this).is(':checked')){
                $(".rowsEditorRow").each(function(){
                    $(this).prop('checked',true);
                });
            }else{
                $(".rowsEditorRow").each(function(){
                    $(this).prop('checked',false);
                });
            }
        });
        $( ".rowsEditorBtn" ).click(function() {
            RE.go(
                $(this).attr('action'),
                $(this).attr('col')
            );
        });

    },
    go: function (action,col) {
        var checkedValues = $('input:checkbox:checked').map(function() {
            return $(this).attr('rowEditorId');
        }).get();

        if(checkedValues.length == 0){
            $.alert({
                title: 'Alert!',
                content: 'No selection!',
            });
            return false;
        }


        url = Routing.generate(RE.route,{
            checkedValues: checkedValues,
            action: action,
            col: col
        });

        if(action == 'delete' || action == 'empty'){
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure?',
                buttons: {
                    confirm: function () {
                        RE.query(url);
                    },
                    cancel: function () {

                    }
                }
            });
        }else{
            RE.query(url);
        }
    },

    query: function (url) {
        $.ajax({
            type: 'post',
            url: url
        })
            .done(function (data) {
                if(data[0].length > data[0].length){
                    alert("NOTHING CHANGE")
                }else{
                    window.location.reload();
                }

            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            });
    }
};
module.exports = RE;