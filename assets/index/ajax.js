/**
 * Static function `MyFolder::ajax()`.
 *
 * @param context
 * @param options
 *
 * Melakukan panggilan ajax.
 *
 */
MyFolder.ajax = {}

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
            $(options.selector, context).replaceWith(options.html);
            break;
        case 'append':
            $(options.selector, context).append(options.html);
            break;
        case 'prepend':
            $(options.selector, context).prepend(options.html);
            break;
        case 'addClass':
            $(options.selector, context).addClass(options.value);
            break;
    }
}

/**
 * Implements hook `MyFolder::attachBehaviors()`.
 */
MyFolder.behaviors.ajax = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.ajax.attach(context, settings)');
        let attachBehaviors = false;
        if (typeof settings == 'object' && 'commands' in settings) {
            settings.commands.forEach(function (value, key, array) {
                switch (value.command) {
                    case 'ajax':
                        if (!('_processed' in value)) {
                            value._processed = false;
                        }
                        if (!value._processed) {
                            value._processed = true;
                            attachBehaviors = true;
                            MyFolder.ajax.command(context, value.options);
                        }
                        break;
                }
            })
        }
        if (attachBehaviors) {
            console.log('|-->MyFolder.attachBehaviors(context)');
            MyFolder.attachBehaviors(context);
        }
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
            console.log('woy');
            event.preventDefault();
            let $form = $(this);
            let url = $form.attr('action');
            let form = $form[0];
            const formData = new FormData(form);
            let trigger = $form.data('trigger');
            if (typeof trigger === 'undefined') {
                let trigger = $form.find('input[type=submitt]').get(0)
            }
            if (typeof trigger !== 'undefined') {
                trigger.disabled = true;
                trigger.innerText = 'Waiting';
            }
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
    }
}
