/**
 * Static function `MyFolder::card()`.
 *
 * @param context
 * @param options
 *
 * Melakukan panggilan card.
 *
 */
MyFolder.card = {}

/**
 * Static function MyFolder\card::command().
 *
 * @param context
 * @param options
 *
 * Melakukan eksekusi hasil dari panggilan card kemudian mengeksekusi
 * MyFolder.attachBehaviors() sesuai context.
 */
MyFolder.card.cards = function (context, options) {
    // console.log(context);
    // console.log(options);
    let $offcanvasBody = $(context).find('.offcanvas-body').empty();
    for (i in options.placeholders) {
        $offcanvasBody.append(options.placeholders[i]);
    }
    for (i in options.routes) {
        let info = {url: options.routes[i], context: $offcanvasBody[0]}
        MyFolder.fetch(info);
    }
}

/**
 * Implements hook `MyFolder::attachBehaviors()`.
 */
MyFolder.behaviors.card = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.card.attach(context, settings)');
        if (typeof settings == 'object' && 'commands' in settings) {
            settings.commands.forEach(function (value, key, array) {
                switch (value.command) {
                    case 'cards':
                        if (!('_processed' in value)) {
                            value._processed = false;
                        }
                        if (!value._processed) {
                            value._processed = true;
                            MyFolder.card.cards(context, value.options);
                        }
                        break;
                }
            })
        }
    }
}
