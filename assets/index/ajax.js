/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.ajax = {
    execute: function (options) {
        let result = {
            commands: [
                {
                    command: 'ajax',
                    options: options
                }
            ]
        }
        new MyFolder.ajax(null, document, {result: result, resultProcessed: false})
    }
}

/**
 * Implements hook `MyFolder.attachBehaviors()`.
 */
MyFolder.behaviors.ajax = {
    attach: function (context, settings) {
        $('button.ajax', context).once('ajax').click(function (event) {
            event.preventDefault();
            let target = $(this).data('target')
            let $form = $(target);
            $form.data('trigger', this);
            $form.find('input[type=submit]').click();
        })
        $('form.ajax', context).once('ajax').submit(function (event) {
            event.preventDefault();
            let $form = $(this);
            let url = $form.attr('action');
            let form = $form[0];
            let base = form.id;
            const formData = new FormData(form);
            let settings = {url: url}
            settings.options = {
                method: 'POST',
                body: formData
            }
            let trigger = $form.data('trigger');
            if (typeof trigger === 'undefined') {
                let trigger = $form.find('input[type=submit]').get(0)
            }
            if (typeof trigger !== 'undefined') {
                trigger.disabled = true;
                trigger.innerText = 'Waiting';
            }
            MyFolder.ajax[base] = new MyFolder.ajax(base, form, settings);
            return false;
        })
    }
}
