/**
 * Global function debug().
 *
 * @param object
 *
 * Cetak variable object ke console. Karena variable itu hanya reference
 * bagi object, maka object dijadikan string terlebih dahulu.
 */
debug = function (object) {
    if (typeof object === 'object') {
        let string = JSON.stringify(object);
        let newObject = JSON.parse(string);
        console.warn(newObject);
    }
    else {
        console.log(typeof object);
        console.warn(object);
    }
}

/**
 * Mendefinisikan variable `MyFolder` pada global.
 */
MyFolder = {
    settings: window.settings || {},
    behaviors: {},
    command: {},
    modifier: {}
};

/**
 * Static function `MyFolder.attachBehaviors()`.
 *
 * @param context
 * @param settings
 *
 * Melakukan panggilan kepada setiap callback/function yang mendaftar pada
 * static property `Myfolder.behaviors`.
 * Digunakan terutama jika terdapat perubahan pada DOM. Semacam event
 * dispatcher. Cara ini terinspirasi dari Javascript di Drupal 7.
 *
 * @reference
 *   https://git.drupalcode.org/project/drupal/-/blob/7.x/misc/drupal.js?ref_type=heads
 */
MyFolder.attachBehaviors = function (context, settings) {
    // console.warn('MyFolder.attachBehaviors(context, settings)');
    // console.log(context);
    // console.log(settings);
    context = context || document;
    settings = settings || window.settings;
    let hook = MyFolder.behaviors;
    Object.keys(hook || {}).forEach(function (i) {
      if (typeof hook[i].attach === 'function') {
        try {
          hook[i].attach(context, settings);
        } catch (e) {
          console.log(e);
        }
      }
    })
}

/**
 * Static function `MyFolder.commandExecution()`.
 *
 * @param settings
 *
 * Melakukan panggilan kepada setiap callback/function yang mendaftar pada
 * static property `Myfolder.command`.
 * Digunakan untuk mengeksekusi property `settings.commands`. Semacam event
 * dispatcher. Cara ini terinspirasi dari Javascript di Drupal 7.
 */
MyFolder.commandExecution = function (settings) {
    // console.warn('MyFolder.commandExecution(settings)');
    // console.log(settings);
    settings = settings || MyFolder.settings
    let command;
    try {
        if (typeof settings == 'object' && 'commands' in settings) {
            settings.commands.forEach(function (each) {
                if (!(typeof each === 'object')) {
                    throw 'Array element is not an object.'
                }
                if (!('command' in each)) {
                    throw 'Property `command` is not defined in object.'
                }
                let options = each.options || {};
                command = each.command;
                // console.log('MyFolder.command.'+command+'.execute(options);');
                MyFolder.command[each.command].execute(options);
            })
            MyFolder.settings.commandsExecuted = $.merge(MyFolder.settings.commandsExecuted,settings.commands)
            settings.commands = [];
        }
    }
    catch (e) {
        if (typeof command !== 'undefined') {
            console.error('Error found while `MyFolder.command.'+command+'.execute()` executed.');
        }
        console.error(e);
    }
}

MyFolder.elementModifier = function (element, info) {
    let hook = MyFolder.modifier;
    Object.keys(hook || {}).forEach(function (i) {
      if (typeof hook[i] === 'function') {
        try {
          hook[i](element, info);
        } catch (e) {
          console.log(e);
        }
      }
    })
}

/**
 * Static function `MyFolder::pseudoLink()`
 *
 * @param url
 *
 * Mengubah url yang merupakan pathinfo, menjadi fullurl, dimana telah
 * ditambahkan basepath dan juga pseudo url.
 */
MyFolder.pseudoLink = function (url) {
    return settings.basePath+'/___pseudo'+url;
}

/**
 * Static function `MyFolder::ajaxLink()`
 *
 * Menambah query `?is_ajax=1` pada URL.
 */
MyFolder.ajaxLink = function (url) {
    let newurl = new URL(window.location.origin+url);
    newurl.searchParams.append('is_ajax','1');
    return newurl.href;
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.settings = {
    execute: function (options) {
        $.extend(true, window.settings, options);
        MyFolder.commandExecution();
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.fetchScript = {
    execute: function (options) {
        let url = options.url;
        url = MyFolder.pseudoLink(url);
        url = MyFolder.ajaxLink(url);
        $.getScript({url: url});
    }
}

/**
 * Implements hook `MyFolder.commandExecution()`.
 */
MyFolder.command.fetch = {
    execute: function (options) {
        let url = MyFolder.pseudoLink(options.url);
        new MyFolder.ajax(null, document, {url: url})
    }
}

/**
 * Implements hook `MyFolder.attachBehaviors()`.
 */
MyFolder.behaviors.pseudoLink = {
    attach: function (context, settings) {
        $('nav.navbar ul a:not(".pseudolink-processed")').each(function (index, element) {
            let href = $(this).attr('href');
            let url = MyFolder.pseudoLink(href);
            $(this).attr('href', url).addClass('pseudolink-processed');
        })
        $('form:not(".pseudolink-processed")').each(function (index, element) {
            let action = $(this).attr('action');
            let url = MyFolder.pseudoLink(action);
            $(this).attr('action', url).addClass('pseudolink-processed');
        })
    }
}

/**
 * Implements hook `MyFolder.attachBehaviors()`.
 */
MyFolder.behaviors.toggle = {
    attach: function (context, settings) {
        $('[data-myfolder-toggle=modal]').once('myfolder-modal').on('click', function (event) {
            event.preventDefault();
            let datasetModal = false;
            let datasetHasModalName = false;
            if ("dataset" in this) {
                if ("myfolderToggle" in this.dataset) {
                    if (this.dataset.myfolderToggle == 'modal') {
                        datasetModal = true;
                    }
                }
                if ("myfolderModalName" in this.dataset) {
                    datasetHasModalName = true;
                    name = this.dataset.myfolderModalName
                }
            }
            if (datasetModal && datasetHasModalName) {
                if (name in MyFolder.modal.load().registry.byName) {
                    MyFolder.modal.load().toggle(name);
                }
                else {
                    // Cari tahu link nya.
                    url = $(this).attr('href');
                    let id = this.id || 'blank';
                    MyFolder.ajax[id] = new MyFolder.ajax(id, this, {url: url});
                }
            }
            return false;
        });
        $('[data-myfolder-toggle=offcanvas]').once('myfolder-offcanvas').on('click', function (event) {
            event.preventDefault();
            let datasetOffcanvas = false;
            let datasetHasOffcanvasName = false;
            if ("dataset" in this) {
                if ("myfolderToggle" in this.dataset) {
                    if (this.dataset.myfolderToggle == 'offcanvas') {
                        datasetOffcanvas = true;
                    }
                }
                if ("myfolderOffcanvasName" in this.dataset) {
                    datasetHasOffcanvasName = true;
                    name = this.dataset.myfolderOffcanvasName
                }
            }
            if (datasetOffcanvas && datasetHasOffcanvasName) {
                if (name in MyFolder.offcanvas.load().registry.byName) {
                    MyFolder.offcanvas.load().toggle(name);
                }
                else {
                    // Cari tahu link nya.
                    url = $(this).attr('href');
                    let id = this.id || 'blank';
                    MyFolder.ajax[id] = new MyFolder.ajax(id, this, {url: url});
                }
            }
        });
    }
}

/**
 * Eksekusi jika document sudah ready.
 */
$(document).ready(function () {
    MyFolder.attachBehaviors();
    MyFolder.commandExecution();
    $('a.navbar-brand').attr('href',MyFolder.settings.basePath);
});
