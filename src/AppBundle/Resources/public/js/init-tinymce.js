var initTinyMce = {
    text: function(selector,css){

        tinymce.init({
            selector:selector,
            width: "100%",
            height: 300,
            //language: 'fr_FR',
            forced_root_block : "",
            verify_html : false,
            plugins: 'code print preview searchreplace autolink directionality  visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
            toolbar1: 'code formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
            content_css: css,

            file_browser_callback_types: 'file image media',

            file_picker_types: 'file image media',
            images_upload_url: '/app_dev.php/app/ajax/post-acceptor',
            images_upload_base_path: '/'
        });

    },

    css: function(selector, css){
        tinymce.init({
            selector: selector,
            width: "100%",
            height: 300,
            //language: 'fr_FR',
            verify_html : false,
            forced_root_block : "",
            toolbar: false,
            content_css: css,
            plugins: [
                'code'
            ]
        });
    }

};