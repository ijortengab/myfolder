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
        // Cara agar tombol submit yang terpaksa berada diluar context <form>
        // kita gunakan attribute data-target pada button tersebut.
        $('button.ajax', context).once('ajax').click(function (event) {
            let target = $(this).data('target')
            if (typeof target !== 'undefined') {
                event.preventDefault();
                let $form = $(target);
                $form.data('trigger', $(this));
                $form.find('input[type=submit]').click();
            }
        })
        // @todo. jika koneksi lambat dan user mengirim berkali-kali.
        // perlu di buat waiting..
        $('form.ajax input[type=radio]', context).once('ajax').click(function (event) {
            let target = $(this).data('target')
            let $form = $(target);

            // Set not finish.
            let isSending = $form.data('isSending')
            if (typeof isSending === 'undefined') {
                $(this).data('isSending', false)
                isSending = false;
            }
            if (isSending) {
                $form.data('isUpdate', true);
            }
            else {
                let name = $(this).attr('name');
                let selector = 'input[type=radio][name='+name+']';
                let $allRadios = $(this).parents('form').find(selector);
                $form.data('trigger', $allRadios);
                $form.find('input[type=submit]').click();
                $form.data('isSending', true)
            }
        })
        $('form.ajax', context).once('ajax').submit(function (event) {
            event.preventDefault();
            let $form = $(this);
            let method = this.method;
            let url = $form.attr('action');
            let form = $form[0];
            let base = form.id;
            const formData = new FormData(form);
            let settings = {
                url: url,
                options: {
                    method: method
                }
            }
            let $trigger = $form.data('trigger');
            if (typeof $trigger === 'undefined') {
                $trigger = $form.find('input[type=submit]')
            }
            if (typeof $trigger !== 'undefined') {
                $trigger.prop("disabled", true);
                let name = $trigger.attr("name");
                if (typeof name !== 'undefined') {
                    // Maka merupakan element form.
                    let value = $trigger.attr("value");
                    formData.append(name, value);
                }
            }
            // method get tidak boleh terdapat body.
            if (method == 'post') {
                settings.options.body = formData
            }
            MyFolder.ajax[base] = new MyFolder.ajax(base, form, settings);
            return false;
        })
    }
}
