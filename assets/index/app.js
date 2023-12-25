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
        console.log(newObject);
    }
    else {
        console.log(typeof object);
        console.log(object);
    }
}

/**
 * Mendefinisikan variable `MyFolder` pada global.
 */
window.MyFolder = {
    settings: window.settings,
    behaviors: {}
};

/**
 * Static function `MyFolder::attachBehaviors()`.
 *
 * @param context
 * @param settings
 *
 * Melakukan panggilan kepada setiap callback/function yang mendaftar pada
 * static property `Myfolder.behaviors`.
 * Digunakan terutama jika terdapat perubahan pada DOM. Semacam event
 * dispatcher. Cara ini terinspirasi dari Javascript di Drupal 7.
 */
MyFolder.attachBehaviors = function (context, settings) {
    console.error('MyFolder.attachBehaviors(context, settings)');
    context = context || document;
    settings = settings || MyFolder.settings;
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

MyFolder.ajaxLink = function (url) {
    let newurl = new URL(window.location.origin+url);
    newurl.searchParams.append('is_ajax','1');
    return newurl.href

}

/**
 * Implements hook `MyFolder::attachBehaviors()`.
 */
MyFolder.behaviors.pseudoLink = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.pseudoLink.attach(context, settings)');
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
 * Static function `MyFolder\modal::register()`.
 *
 * @param info
 *
 * Menambahkan informasi modal kedalam registry.
 */
MyFolder.behaviors.toggleBootstrapComponent = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.toggleBootstrapComponent.attach(context, settings)');
        $('[data-myfolder-toggle=modal]').once('myfolder-modal').on('click', function () {
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
                    console.log('> MyFolder.modal.load().toggle(name);');
                    MyFolder.modal.load().toggle(name);
                }
                else {
                    // Cari tahu link nya.
                    url = $(this).attr('href');
                    // MyFolder.ajax(this, url);
                    let info = {
                        url: url,
                        _pseudoLink: true
                    }
                    console.log('> MyFolder.fetch(info);');
                    MyFolder.fetch(info);
                }
            }
        });
        $('[data-myfolder-toggle=offcanvas]').once('myfolder-offcanvas').on('click', function () {
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
                    console.log('> MyFolder.offcanvas.load().toggle(name);');
                    MyFolder.offcanvas.load().toggle(name);
                }
                else {
                    // Cari tahu link nya.
                    url = $(this).attr('href');
                    // MyFolder.ajax(this, url);
                    let info = {
                        url: url,
                        _pseudoLink: true
                    }
                    console.log('> MyFolder.fetch(info);');
                    MyFolder.fetch(info);
                }
            }
        });
    }
}

MyFolder.behaviors.fetch = {
    attach: function (context, settings) {
        if (typeof settings == 'object') {
            if ('commands' in settings) {
                settings.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'fetch':
                            if (!('_processed' in value)) {
                                value._processed = false;
                            }
                            if (!value._processed) {
                                value._processed = true;
                                MyFolder.fetch(value.options);
                            }
                            break;
                        case 'fetchScript':
                            if (!('_processed' in value)) {
                                value._processed = false;
                            }
                            if (!value._processed) {
                                value._processed = true;
                                value.options.url = MyFolder.pseudoLink(value.options.url)
                                $.getScript(value.options);
                            }
                            break;
                    }
                })
            }
        }
    }
}

MyFolder.fetch = function (info) {
    if (!('_pseudoLink' in info)) {
        info._pseudoLink = false;
    }
    if (!info._pseudoLink) {
        info._pseudoLink = true;
        info.url = MyFolder.pseudoLink(info.url);
    }
    if (!('_ajaxLink' in info)) {
        info._ajaxLink = false;
    }
    if (!info._ajaxLink) {
        info._ajaxLink = true;
        info.url = MyFolder.ajaxLink(info.url);
    }
    let url = info.url;
    let options = info.options || {}
    let promise = fetch(url, options)
        .then(function(response){
            return response.json()
        })
        .then(function(result){
            if ('commands' in settings) {
                if ('commands' in result) {
                    settings.commands = $.merge(settings.commands,result.commands)
                }
            }
            if ('context' in info) {
                console.log('> MyFolder.attachBehaviors(info.context)');
                MyFolder.attachBehaviors(info.context);
            }
            else {
                console.log('> MyFolder.attachBehaviors()');
                MyFolder.attachBehaviors()
            }
        })
        .catch(function(error){
            console.error(error)
        });
}

/**
 * Eksekusi jika document sudah ready.
 */
$(document).ready(function () {
    MyFolder.attachBehaviors()
    $('a.navbar-brand').attr('href',MyFolder.settings.basePath);
});
