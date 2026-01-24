MyFolder = window.MyFolder || {}
MyFolder.index.filter = {
    $input: $('input[type=search]'),
    timeout: undefined,
    fnmatch: function (glob, input) {
        const matcher = glob
            .replace(/\*/g, '.*')
            .replace(/\?/g, '.'); // Replace wild cards with regular expression equivalents
        const r = new RegExp(`${ matcher }`, "ig"); // Match beginning and end of input using ^ and $
        return r.test(input);
    }
}
MyFolder.index.filter.$buttonTrigger = $([
    '<button style="cursor:pointer;"class="form-control me-2" type="search" placeholder="" aria-label="Search">',
    'Type ',
    '<kbd style="background-color: rgba(0, 0, 0, 0.125);color: var(--bs-body-color);">/</kbd>',
    ' to search',
    '</button>'
].join('')).css('width',MyFolder.index.filter.$input.css('width')).prependTo(MyFolder.index.filter.$input.parent());
MyFolder.index.filter.shortcut = function (e) {
    if (e.key === '/') {
        e.preventDefault();
        $(document).off('keydown', MyFolder.index.filter.shortcut);
        MyFolder.index.filter.$input.focus();
    }
}
MyFolder.index.filter.first = function () {
    const instance = MyFolder.index.instance
    let $first = instance.$tbody.find('tr:visible').first();
    if ($first.length) {
        let $a = $first.find('td.name a');
        let infoType = $a.data('infoType');
        if (infoType == '.') {
            // Dikosongkan karena akan di redraw table oleh
            // history API.
            $(this).val('');
            $a.click();
        }
        else {
            let href = $a.attr('href');
            location.href = href
        }
    }
}
MyFolder.index.filter.$input.on('focus', function () {
    if (MyFolder.index.filter.$buttonTrigger.is(':visible')) {
        MyFolder.index.filter.$buttonTrigger.click();
    }
}).on('blur', function () {
    $(document).on('keydown', MyFolder.index.filter.shortcut);
    let val = $(this).val().trim();
    if (!MyFolder.index.filter.$buttonTrigger.is(':visible') && val === '') {
        MyFolder.index.filter.$buttonTrigger.show();
        MyFolder.index.filter.$input.hide();
    }
}).on('keyup', function (e) {
    if (MyFolder.index.filter.timeout) {
        clearTimeout(MyFolder.index.filter.timeout);
    }
    const instance = MyFolder.index.instance;
    let val = $(this).val().trim();
    if (val === '/') {
        $(this).val('')
        return;
    }
    let that = this;
    if (val === '') {
        if (e.keyCode == 13) {
            $(this).val('')
            MyFolder.index.filter.first()
        }
        else {
            instance.$tbody.find('tr').show()
        }
    }
    else if (e.keyCode == 13) {
        $(this).val('')
        MyFolder.index.filter.first()
    }
    else {
        MyFolder.index.filter.timeout = setTimeout(function () {
            if (val.endsWith('/')) {
                $(that).val('');
                let directory = MyFolder.settings.pathInfo + '/' + val
                // Bersihkan dari double slash.
                let directorySanitized = directory.replace(/\/+/g,'/');
                // Sanitasi /blog/././ menjadi /blog/
                do {
                    directorySanitized = directorySanitized.replace(/\/\.\//g, '/');
                } while (directorySanitized.includes('/./'));
                // User boleh mengetik double dot: `..` pada input search,
                let directorySanitizedArray = directorySanitized.replace(/^\//,'').replace(/\/$/,'').split('/');
                let directoryResolved = [];
                let directoryEncodedResolved = [];
                while (directorySanitizedArray.length > 0) {
                    let each = directorySanitizedArray.shift();
                    if (each === '..') {
                        directoryResolved.pop();
                        directoryEncodedResolved.pop();
                    } else {
                        directoryResolved.push(each);
                        directoryEncodedResolved.push(encodeURIComponent(each));
                    }
                }
                MyFolder.settings.pathInfo = directoryResolved.length === 0 ? '/' : '/' + directoryResolved.join('/') + '/';
                MyFolder.settings.pathInfoEncoded = directoryEncodedResolved.length === 0 ? '/' : '/' + directoryResolved.join('/') + '/';
                let state = {pathInfo: MyFolder.settings.pathInfo, pathInfoEncoded: MyFolder.settings.pathInfoEncoded}
                window.history.pushState(state, "", MyFolder.settings.basePath + MyFolder.settings.pathInfoEncoded);
                MyFolder.index.instance = new MyFolder.index();
            }
            else {
                let filteredArray = instance.ls_result.filter(function (currentValue) {
                    if (val.includes('*') || val.includes('?')) {
                        return MyFolder.index.filter.fnmatch(val, currentValue)
                    }
                    return currentValue.toLowerCase().includes(val.toLowerCase())
                })
                instance.$tbody.find('tr').hide().each(function () {
                    let name = $(this).data('infoName')
                    if (filteredArray.find(value => value === name)) {
                        $(this).show();
                    };
                })
            }
        },250)
    }
}).hide();
MyFolder.index.filter.$buttonTrigger.on('click', function (e) {
    e.preventDefault();
    MyFolder.index.filter.$buttonTrigger.hide();
    MyFolder.index.filter.$input.show().focus()
})

$(document).on('keydown', MyFolder.index.filter.shortcut);

// Tambah support untuk modal.
let primary = document.getElementById('ModalPrimary');
primary.addEventListener('show.bs.modal', event => {
    // console.log('show.bs.modal primary');
    $(document).off('keydown', MyFolder.index.filter.shortcut);
})
primary.addEventListener('hide.bs.modal', event => {
    // console.log('hide.bs.modal primary');
    $(document).on('keydown', MyFolder.index.filter.shortcut);
})

let secondary = document.getElementById('ModalSecondary');
secondary.addEventListener('show.bs.modal', event => {
    // console.log('show.bs.modal secondary');
    $(document).off('keydown', MyFolder.index.filter.shortcut);
})
secondary.addEventListener('hide.bs.modal', event => {
    // console.log('hide.bs.modal secondary');
    $(document).off('keydown', MyFolder.index.filter.shortcut);
})
