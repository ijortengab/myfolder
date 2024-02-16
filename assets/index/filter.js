MyFolder = window.MyFolder || {}

MyFolder.index.filter = {
    $input: $('input[type=search]'),
    timeout: undefined,
    fnmatch: function (glob, input) {
        const matcher = glob
            .replace(/\*/g, '.*')
            .replace(/\?/g, '.'); // Replace wild cards with regular expression equivalents
        const r = new RegExp(`^${ matcher }$`); // Match beginning and end of input using ^ and $
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
        instance.$tbody.find('tr').show()
    }
    else {
        // @todo, jika user mengetik ..
        // maka bisa mengindex diluar jail root.
        MyFolder.index.filter.timeout = setTimeout(function () {
            if (val.endsWith('/')) {
                $(that).val('');
                MyFolder.settings.pathInfo = MyFolder.settings.pathInfo + val;
                if (MyFolder.settings.rewriteUrl) {
                    history.pushState({pathInfo: MyFolder.settings.pathInfo}, "", val);
                }
                else {
                    history.pushState({pathInfo: MyFolder.settings.pathInfo}, "", MyFolder.settings.basePath+MyFolder.settings.pathInfo);
                }
                MyFolder.index.instance = new MyFolder.index();
            }
            else {
                let filteredArray = instance.ls_result.filter(function (currentValue) {
                    if (val.includes('*') || val.includes('?')) {
                        return MyFolder.index.filter.fnmatch(val, currentValue)
                    }
                    return currentValue.includes(val)
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
