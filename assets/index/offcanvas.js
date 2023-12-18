// Object wrapper untuk menghandle multiple offcanvas.
// Reference: https://getbootstrap.com/docs/5.3/components/offcanvas/#toggle-between-offcanvass
MyFolder.Offcanvas = function () {
    this.register = MyFolder.Offcanvas.register;
    this.currentIndex = 0;
    this.currentOffcanvas;
    this.otherOffcanvas;
    this.primary;
    this.secondary;
    this.scripts = [];
    this.isLastOffcanvas = false;
}
MyFolder.Offcanvas.load = function () {
    if (!("instance" in MyFolder.Offcanvas)) {
        MyFolder.Offcanvas.instance = new MyFolder.Offcanvas();
    }
    return MyFolder.Offcanvas.instance;
}
// List of dynamice property:
// MyFolder.Offcanvas.register => object referece
// MyFolder.Offcanvas.instance => object instance of MyFolder.Offcanvas
MyFolder.Offcanvas.isBlocking = undefined;
MyFolder.Offcanvas.prototype.reset = function () {
    this.currentIndex = 0;
    this.register.byQueue = [];
    this.primary = undefined;
    this.secondary = undefined;
    this.currentOffcanvas = undefined;
    this.otherOffcanvas = undefined;
    this.isLastOffcanvas = false;
}
MyFolder.Offcanvas.prototype.toggle = function (name) {
    console.log('this.register.byQueue', JSON.stringify(this.register.byQueue));
    console.log('this.isLastOffcanvas', JSON.stringify(this.isLastOffcanvas));
    if (this.currentIndex >= (this.register.byQueue.length - 1)) {
        this.isLastOffcanvas = true;
    }
    console.log('this.isLastOffcanvas', JSON.stringify(this.isLastOffcanvas));
    
    const self = this;
    if (typeof name !== 'undefined') {
        this.reset();
        console.log('this.register.byQueue', JSON.stringify(this.register.byQueue));
        this.register.byQueue.push(name);
        console.log('this.register.byQueue', JSON.stringify(this.register.byQueue));
    }
    // return;
    // if (!(this.currentIndex in this.register.byQueue)) {
        // return
    // }
    // console.log('ada');
    let i = this.currentIndex
    let ref = this.register.byQueue[i]
    if (typeof ref == 'string') {
        ref = this.register.byName[ref]
    }
    if (typeof ref == 'undefined') {
        return;
    }
    let offcanvasOptions = ('bootstrapOptions' in ref) ? ref.bootstrapOptions : {};
    console.log('this.primary', JSON.stringify(typeof this.primary));
    if (typeof this.primary === 'undefined') {
        this.preparePrimary();
        this.primary = new bootstrap.Offcanvas('#OffcanvasPrimary', offcanvasOptions);
        this.currentOffcanvas = this.primary;
    }
    else if (typeof this.secondary === 'undefined') {
        this.secondary = new bootstrap.Offcanvas('#OffcanvasSecondary', offcanvasOptions);
        this.prepareSecondary();
        this.currentOffcanvas = this.secondary;
        this.otherOffcanvas = this.primary;
    }
    else if (this.currentOffcanvas._element.id == 'OffcanvasPrimary') {
        this.currentOffcanvas = this.secondary;
        this.otherOffcanvas = this.primary;
    }
    else {
        this.currentOffcanvas = this.primary;
        this.otherOffcanvas = this.secondary;
    }
    if ('layout' in ref) {
        let size = ('size' in ref.layout) ? ref.layout.size : '';
        let title = ('title' in ref.layout) ? ref.layout.title : '';
        let body = ('body' in ref.layout) ? ref.layout.body : '';
        let footer = ('footer' in ref.layout) ? ref.layout.footer : '';
        this.setSize(size).setTitle(title).setBody(body).setFooter(footer);
        if ('ajax' in ref.layout) {
            if ('url' in ref.layout.ajax) {
                let url = MyFolder.pseudoLink(ref.layout.ajax.url);
                ref.layout.ajax.url = url;
            }
            console.log('> MyFolder.ajax(this.currentOffcanvas._element, ref.layout.ajax);');
            MyFolder.ajax(this.currentOffcanvas._element, ref.layout.ajax);
        }
        else {
            console.log('> MyFolder.attachBehaviors(this.currentOffcanvas._element);');
            MyFolder.Offcanvas.isBlocking = true;
            console.log('MyFolder.Offcanvas.isBlocking', JSON.stringify(MyFolder.Offcanvas.isBlocking));
            MyFolder.attachBehaviors(this.currentOffcanvas._element);
        }
    }
    
    // this.currentOffcanvas._element.addEventListener('hide.bs.offcanvas', function (e) {
    // })
    const myOffcanvasEl = this.currentOffcanvas._element
    myOffcanvasEl.addEventListener('shown.bs.offcanvas', event => {
        console.log('Event listened shown.bs.offcanvas.');
        MyFolder.Offcanvas.isBlocking = false;
        console.log('MyFolder.Offcanvas.isBlocking', JSON.stringify(MyFolder.Offcanvas.isBlocking));
    })
    myOffcanvasEl.addEventListener('hidden.bs.offcanvas', event => {
        console.log('Event listened hidden.bs.offcanvas.');
        // console.log('this.register.byQueue', JSON.stringify(this.register.byQueue));
        // console.log('this.currentIndex', JSON.stringify(this.currentIndex));
        console.log('> self.next();');
        MyFolder.Offcanvas.isBlocking = true;
        self.next();
        // if (typeof self.otherOffcanvas !== 'undefined') {
            // console.log('> self.otherOffcanvas.toggle();');
            // self.otherOffcanvas.toggle();
        // }
    })
    
    

    // console.log(this);
    console.log('> this.currentOffcanvas.toggle();');
    this.currentOffcanvas.toggle();
    
    // if (this.lastOffcanvas) {
        // console.log('> this.reset();');
        // this.reset();
        // this.isLastOffcanvas = true;
    // }
}
MyFolder.Offcanvas.prototype.next = function () {
    const self = this;
    const myOffcanvasEl = this.currentOffcanvas._element
    console.log('this.next()');
    console.log('this.register.byQueue', JSON.stringify(this.register.byQueue));
    console.log('this.currentIndex', JSON.stringify(this.currentIndex));
    // return;
    // console.log(JSON.stringify(this.isLastOffcanvas));
    // let toggle = false;
    if (this.currentIndex < (this.register.byQueue.length - 1)) {
        this.currentIndex++;
        // toggle = true;
        console.log('this.currentIndex', JSON.stringify(this.currentIndex));
        console.log('> this.toggle()');
        this.toggle();
    }
    else {
        MyFolder.Offcanvas.isBlocking = false;
        console.log('MyFolder.Offcanvas.isBlocking', JSON.stringify(MyFolder.Offcanvas.isBlocking));
    }
    // if (this.currentIndex >= (this.register.byQueue.length - 1)) {
        // this.isLastOffcanvas = true;
    // }
    // console.log(JSON.stringify(this.register.byQueue));
    // console.log(JSON.stringify(this.currentIndex));
    // console.log(JSON.stringify(this.isLastOffcanvas));
    // if (toggle) {
    // }
    // else {
        // if (typeof this.currentOffcanvas !== 'undefined') {
            // console.log('> this.currentOffcanvas.hide();');
            // this.currentOffcanvas.hide();
        // }
        // if (typeof this.otherOffcanvas !== 'undefined') {
            // console.log('> this.otherOffcanvas.hide();');
            // this.otherOffcanvas.hide();
        // }
        // console.log('> this.reset()');
        // this.reset();
    // }
}
MyFolder.Offcanvas.prototype.setSize = function (size) {
    switch (size) {
        case 'Small':
            var classAdded = 'offcanvas-sm';
            break;
        case 'Large':
            var classAdded = 'offcanvas-lg';
            break;
        case 'Extra large':
            var classAdded = 'offcanvas-xl';
            break;
    }
    if (classAdded !== '') {
        $(this.currentOffcanvas._dialog).removeClass('offcanvas-sm offcanvas-lg offcanvas-xl').addClass(classAdded);
    }
    return this;
}
MyFolder.Offcanvas.prototype.setTitle = function (title) {
    if (typeof title === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-title').text(title);
    }
    else {
        let titleHtml = (typeof title.html !== 'undefined') ? title.html : '';
        if (titleHtml !== '') {
            $(this.currentOffcanvas._element).find('.offcanvas-title').html(titleHtml);
        }
    }
    return this;
}
MyFolder.Offcanvas.prototype.setBody = function (body) {
    if (typeof body === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-body').empty().text(body);
    }
    let bodyHtml = (typeof body.html !== 'undefined') ? body.html : '';
    if (bodyHtml !== '') {
        $(this.currentOffcanvas._element).find('.offcanvas-body').html(bodyHtml);
    }
    return this;
}
MyFolder.Offcanvas.prototype.setFooter = function (footer) {
    if (typeof footer === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-footer').empty().text(footer);
    }
    else {
        let footerHtml = (typeof footer.html !== 'undefined') ? footer.html : '';
        if (footerHtml !== '') {
            $(this.currentOffcanvas._element).find('.offcanvas-footer').html(footerHtml);
        }
    }
    return this;
}
MyFolder.Offcanvas.prototype.preparePrimary = function (element) {
    return;
    // const self = this
    // $('#OffcanvasPrimary .offcanvas-header button').on('click',function (e) {
        // console.log('self.isLastOffcanvas',JSON.stringify(self.isLastOffcanvas));
        // if (self.isLastOffcanvas) {
        // }
        // else {
            // self.next();
        // }
    // });
}
MyFolder.Offcanvas.prototype.prepareSecondary = function (element) {
    return;
    // const self = this
    // $('#OffcanvasSecondary .offcanvas-header button').on('click',function (e) {
        // console.log('self.isLastOffcanvas',JSON.stringify(self.isLastOffcanvas));
        // if (self.isLastOffcanvas) {
        // }
        // else {
            // self.next();
        // }
    // });
}
MyFolder.behaviors.offcanvas = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.offcanvas.attach(context, settings)');
        console.log('MyFolder.Offcanvas.isBlocking', JSON.stringify(MyFolder.Offcanvas.isBlocking));
        if (typeof MyFolder.Offcanvas.isBlocking == 'undefined') {
            isBlocking = false;
        }
        else if (typeof MyFolder.Offcanvas.isBlocking == 'boolean') {
            isBlocking = MyFolder.Offcanvas.isBlocking;
        }
        if (isBlocking) {
            return;
        }
        MyFolder.Offcanvas.isBlocking = true;
        let init = false;
        let next = false;
        let name = undefined;
        let datasetOffcanvas = false;
        let datasetHasOffcanvasName = false;
        if (typeof MyFolder.Offcanvas.register == 'undefined') {
            init = true;
            MyFolder.Offcanvas.register = {
                byName: {},
                byQueue: []
            };
        }
        const register = MyFolder.Offcanvas.register
        if (typeof settings == 'object') {
            if ('commands' in settings) {
                settings.commands.forEach(function (value, key, array) {
                    switch (value.command) {
                        case 'offcanvas':
                            if ("name" in value.options) {
                                console.log('settings.commands.command[offcanvas].options.name', JSON.stringify(value.options.name));
                                name = value.options.name;
                                if (!(name in register.byName)) {
                                    register.byName[name] = value.options;
                                }
                                register.byQueue.push(name);
                            }
                            else {
                                register.byQueue.push(value.options);
                            }
                            next = true;
                            break;
                    }
                })
            }
        }
        if ("dataset" in context) {
            if ("myfolderToggle" in context.dataset) {
                if (context.dataset.myfolderToggle == 'offcanvas') {
                    datasetOffcanvas = true;
                }
            }
            if ("myfolderOffcanvasName" in context.dataset) {
                datasetHasOffcanvasName = true;
                name = context.dataset.myfolderOffcanvasName
            }
        }
        if (name && datasetOffcanvas && datasetHasOffcanvasName) {
            console.log('> MyFolder.Offcanvas.load().toggle(name);');
            MyFolder.Offcanvas.load().toggle(name);
        }
        else if (init && register.byQueue.length > 0) {
            console.log('> MyFolder.Offcanvas.load().toggle();');
            MyFolder.Offcanvas.load().toggle();
        }
        else if (next) {
            console.log('> MyFolder.Offcanvas.load().next();');
            MyFolder.Offcanvas.load().next();
        }
        else {
            MyFolder.Offcanvas.isBlocking = false;
        }
    }
}
MyFolder.behaviors.toggleDashboard = {
    attach: function (context, settings) {
        console.log('|-MyFolder.behaviors.toggleDashboard.attach(context, settings)');
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
                if (name in MyFolder.Offcanvas.load().register.byName) {
                    console.log('> MyFolder.Offcanvas.load().toggle(name);');
                    MyFolder.Offcanvas.load().toggle(name);
                }
                else {
                    // Cari tahu link nya.
                    url = $(this).attr('href');
                    console.log('> MyFolder.ajax(this, url);');
                    MyFolder.ajax(this, url);
                }
            }
        });
    }
}