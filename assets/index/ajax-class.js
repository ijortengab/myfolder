/**
 * Ajax object.
 *
 * @param base
 * @param element
 * @param element_settings
 *
 * @reference
 *   https://git.drupalcode.org/project/drupal/-/blob/7.x/misc/ajax.js?ref_type=heads
 */
MyFolder.ajax = function (base, element, element_settings) {
    $.extend(this, element_settings);
    if ('url' in this) {
        let url = MyFolder.ajaxLink(this.url);
        let options = this.options || {};
        let ajax = this;
        const promise = fetch(url, options)
            .then(function(response){
                return response.json()
            })
            .then(function (result) {
                ajax.enableTrigger(element);
                ajax.result = result
                ajax.resultProcessed = false
                ajax.resultProcess();
            })
            .catch(function(error){
                console.error(error)
            });
    }
    if ('result' in this) {
        if (!(this.resultProcessed)) {
            this.resultProcessed = true;
            this.resultProcess();
        }
    }
}
MyFolder.ajax.prototype.resultProcess = function () {
    let commands = this.result.commands;
    let newCommands = [];
    let each;
    while (commands.length) {
        each = commands.shift();
        if (each.command == 'ajax') {
            this.commands[each.options.method](this, each.options);
            switch (each.options.method) {
                case 'html':
                case 'append':
                case 'replaceWith':
                case 'prepend':
                case 'addClass':
                    MyFolder.attachBehaviors(this.element);
                    break;
            }
        }
        else {
            newCommands.push(each);
        }
    }
    if (newCommands.length) {
        MyFolder.commandExecution({commands: newCommands})
    }
};
MyFolder.ajax.prototype.commands = {
    remove: function (ajax, response) {
        $(response.selector, ajax.element).remove();
    },
    html: function (ajax, response) {
        $(response.selector, ajax.element).html(response.html);
    },
    append: function (ajax, response) {
        $(response.selector, ajax.element).append(response.html);
    },
    prepend: function (ajax, response) {
        $(response.selector, ajax.element).prepend(response.html);
    },
    addClass: function (ajax, response) {
        $(response.selector, ajax.element).addClass(response.value);
    },
    fetch: function (ajax, response) {
        let url = MyFolder.pseudoLink(response.url);
        new MyFolder.ajax(null, ajax.element, {url: url})
    },
    replaceWith: function (ajax, response) {
        $(response.selector, ajax.element).replaceWith(response.html);
    }
}
MyFolder.ajax.prototype.enableTrigger = function (element) {
    if (typeof element === 'undefined') {
        return;
    }
    let $trigger = $(element).data('trigger');
    if (typeof $trigger === 'undefined') {
        return;
    }
    $trigger.prop("disabled", false);
    // @todo, jika form , maka
    $(element).data('isSending', false);
    if ($(element).data('isUpdate') === true) {
        $(element).data('isUpdate', false);
        $(element).submit();
    }
}
