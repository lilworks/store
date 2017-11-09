$( document ).ready(function() {

    $('.btn-delete').confirm({
        title: Translator.trans('js.alert.delete.title'),
        content: Translator.trans('js.alert.delete.text'),
        buttons: {
            yes: {
                text: Translator.trans('js.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('js.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });
    $('.btn-unsubscribe').confirm({
        title: Translator.trans('js.alert.unsubscribe.title'),
        content: Translator.trans('js.alert.unsubscribe.text'),
        buttons: {
            yes: {
                text: Translator.trans('js.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('js.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });
    $('.btn-subscribe').confirm({
        title: Translator.trans('js.alert.subscribe.title'),
        content: Translator.trans('js.alert.subscribe.text'),
        buttons: {
            yes: {
                text: Translator.trans('js.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('js.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });

    $('.btn-empty').confirm({
        title: Translator.trans('js.alert.empty.title'),
        content: Translator.trans('js.alert.empty.text'),
        buttons: {
            yes: {
                text: Translator.trans('js.alert.yes'),
                keys: ['y'],
                action: function () {
                    location.href = this.$target.attr('href');
                }
            },
            no: {
                text: Translator.trans('js.alert.no'),
                keys: ['N'],
                action: function () {
                }
            }
        }
    });
});