
$( document ).ready(function() {

    function initAjaxForm(f)
    {

        f.on('submit', function (e) {

            e.preventDefault();

            var currentHidden = f.parent().parent().find('.liveEditorCurrent_hidden');
            var editorShow = f.parent().parent().find('.liveEditor_show');

            url = Routing.generate('lilworks_ajax_liveEditor',{
                entity: editorShow.find('input[name=entity]').val(),
                fieldName: editorShow.find('input[name=fieldName]').val(),
                eid: editorShow.find('input[name=eid]').val(),
                listeners: editorShow.find('input[name=listeners]').val()
            });

            $.ajax({
                type: $(this).attr('method'),
                url: url,
                data: $(this).serialize()
            })
                .done(function (data) {
                    editorShow.attr('class','liveEditor_hidden');
                    currentHidden.html(data);
                    currentHidden.attr('class','liveEditorCurrent_show');

                    setEditingMode( "off", f.parent().parent() );

                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        if (jqXHR.responseJSON.hasOwnProperty('form')) {
                            $('#form_body').html(jqXHR.responseJSON.form);
                        }

                        $('.form_error').html(jqXHR.responseJSON.message);

                    } else {
                        alert(errorThrown);
                    }

                });
        });
    }



    function setEditingMode(mode,o){
        var currentHidden = o.find('.liveEditorCurrent_hidden');
        var currentShow = o.find('.liveEditorCurrent_show');
        var editorHidden = o.find('.liveEditor_hidden');
        var editorShow = o.find('.liveEditor_show');

        if(mode == "on"){


            editorShow.find(".liveEditor_btn_cancel").bind( "click", function( event ) {
                editorShow.attr('class','liveEditor_hidden');
                currentHidden.attr('class','liveEditorCurrent_show');
                setEditingMode("off",o);
            });

        }else if(mode == "off"){

            currentShow.bind( "click", function( event ) {
                editorHidden.attr('class','liveEditor_show');
                $(this).attr('class','liveEditorCurrent_hidden');
                $(this).unbind();
                setEditingMode("on",o);
            });

        }

    }

    $( ".lilworks_liveEditor" ).each(function( ) {
        console.log($(this));
        initAjaxForm($(this).find('form'));
        setEditingMode( "off", $( this ) );
    });


});