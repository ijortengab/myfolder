/**
 * Static function `MyFolder::ajax()`.
 *
 * @param context
 * @param options
 *
 * Melakukan panggilan ajax.
 *
 */
MyFolder.ajax = function (context, options) {
    console.error('DEPRACATED');
    return;
    let xhr;
    if (typeof options == 'string') {
        options = {
            type: 'GET',
            url: options
        }
    }
    if ('url' in options) {
        // Tambahkan $_GET[ajax] agar dikenali oleh Controller.
        let ajaxify = new URL(window.location.origin+options.url);
        ajaxify.searchParams.append('_ajax','1');
        options.url = ajaxify.href
    }
    xhr = $.ajax(options);
    xhr.done(function (data) {
        // Invoke akan dilakukan oleh instance object MyFolder.ajax.command.
        let invoke = true;
        if (typeof data == 'object') {
            if ('commands' in data) {
                data.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'ajax':
                            new MyFolder.ajax.command(context, value.options);
                            invoke = false;
                            break;
                    }
                })
            }
        }
        if (invoke) {
            MyFolder.attachBehaviors(context, data);
        }
    })
}

/**
 * Static function MyFolder\ajax::command().
 *
 * @param context
 * @param options
 *
 * Melakukan eksekusi hasil dari panggilan ajax kemudian mengeksekusi
 * MyFolder.attachBehaviors() sesuai context.
 */
MyFolder.ajax.command = function (context, options) {
    switch (options.method) {
        case 'remove':
            $(options.selector, context).remove();
            break;
        case 'replace':
            $(options.selector, context).html(options.html);
            break;
        case 'append':
            $(options.selector, context).append(options.html);
            break;
        case 'addClass':
            $(options.selector, context).addClass(options.value);
            break;
        case 'script':
            options.ajax.dataType = 'script'
            if ('url' in options.ajax) {
                options.ajax.url = MyFolder.settings.basePath+'/___pseudo'+options.ajax.url
            }
            return MyFolder.ajax(context, options.ajax);
    }
    MyFolder.attachBehaviors(context);
}

/**
 * Implements hook `MyFolder::attachBehaviors()`.
 */
MyFolder.behaviors.ajax = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.ajax.attach(context, settings)');
        $('a.ajax').once('ajax').click(function () {
            event.preventDefault();
            let $element = $(this);
            MyFolder.ajax(this, {
                url: $element.attr('href'),
                type: 'GET',
            });
        })
        $('button.ajax', context).once('ajax').click(function () {
            event.preventDefault();
            let target = $(this).data('target')
            let $form = $(target);
            $form.data('trigger', this);
            $form.find('input[type=submit]').click();
        })
        $('form.ajax', context).once('ajax').submit(function () {
            event.preventDefault();
            let $form = $(this);
            let url = $form.attr('action');
            let form = $form[0];
            const formData = new FormData(form);
            let trigger = $form.data('trigger');
            trigger.disabled = true;
            trigger.innerText = 'Waiting';
            let info = {
                url: url,
                _pseudoLink: true,
                options: {
                    method: "POST",
                    body: formData
                }
            }
            MyFolder.fetch(info);
            return false;
        })
        if (typeof settings == 'object') {
            if ('commands' in settings) {
                settings.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'ajax':
                            if (!('_isExecuted' in value.options)) {
                                value.options._isExecuted = false;
                            }
                            if (!value.options._isExecuted) {
                                value.options._isExecuted = true;
                                MyFolder.ajax.command(context, value.options);
                            }
                            break;
                    }
                })
            }
        }
    }
}
