$( document ).ready(function() {
    $('.btn-delete').confirm({
        title: Translator.trans('lilworks.alert.delete.title'),
        content: Translator.trans('lilworks.alert.delete.text'),
        buttons: {
            yes: {
                text: Translator.trans('lilworks.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('lilworks.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });
    $('.btn-empty').confirm({
        title: Translator.trans('lilworks.alert.empty.title'),
        content: Translator.trans('lilworks.alert.empty.text'),
        buttons: {
            yes: {
                text: Translator.trans('lilworks.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('lilworks.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });
});