// Object wrapper untuk menghandle multiple offcanvas.
// Reference: https://getbootstrap.com/docs/5.3/components/offcanvas/#placement
// Untuk mendapatkan object dari class ini, gunakan method `MyFolder.offcanvas.load()`
// yang akan me-return property `MyFolder.offcanvas.instance`.
MyFolder.offcanvas = function () {
    this.registry = MyFolder.offcanvas.registry;
    this.currentIndex = 0;
    this.currentName;
    this.currentOffcanvas;
    this.otherOffcanvas;
    this.offcanvas;
    this.offcanvasTop;
    this.offcanvasRight;
    this.offcanvasBottom;
    this.pauseToggle = false;
    this.isLastOffcanvas = false;
    // event listener.
    let offcanvas = document.getElementById('offcanvas');
    offcanvas.addEventListener('hide.bs.offcanvas', event => {
        console.log('hide.bs.offcanvas offcanvas');
        const self = MyFolder.offcanvas.load();
        self.next();
    })
    offcanvas.addEventListener('hidden.bs.offcanvas', event => {
        console.log('hidden.bs.offcanvas offcanvas');
        const self = MyFolder.offcanvas.load();
        if (self.pauseToggle) {
            self.pauseToggle = false;
            self.currentOffcanvas.toggle();
        }
        // Delete all accesories.
        // $(self.primary._dialog).removeClass('offcanvas-fullscreen offcanvas-sm offcanvas-lg offcanvas-xl');
        // Reset.
        // if (self.isLastOffcanvas) {
            // self.reset();
        // }
    })
    let offcanvasTop = document.getElementById('offcanvasTop');
    offcanvasTop.addEventListener('hide.bs.offcanvas', event => {
        console.log('hide.bs.offcanvas offcanvasTop');
        const self = MyFolder.offcanvas.load();
        self.next();
    })
    offcanvasTop.addEventListener('hidden.bs.offcanvas', event => {
        console.log('hidden.bs.offcanvas offcanvasTop');
        const self = MyFolder.offcanvas.load();
        if (self.pauseToggle) {
            self.pauseToggle = false;
            self.currentOffcanvas.toggle();
        }
        // const self = MyFolder.offcanvas.load();
        // Delete all accesories.
        // $(self.primary._dialog).removeClass('offcanvas-fullscreen offcanvas-sm offcanvas-lg offcanvas-xl');
        // Reset.
        // if (self.isLastOffcanvas) {
            // self.reset();
        // }
    })
    let offcanvasRight = document.getElementById('offcanvasRight');
    offcanvasRight.addEventListener('hide.bs.offcanvas', event => {
        console.log('hide.bs.offcanvas offcanvasRight');
        const self = MyFolder.offcanvas.load();
        self.next();
    })
    offcanvasRight.addEventListener('hidden.bs.offcanvas', event => {
        console.log('hidden.bs.offcanvas offcanvasRight');
        const self = MyFolder.offcanvas.load();
        if (self.pauseToggle) {
            self.pauseToggle = false;
            self.currentOffcanvas.toggle();
        }
        // const self = MyFolder.offcanvas.load();
        // Delete all accesories.
        // $(self.primary._dialog).removeClass('offcanvas-fullscreen offcanvas-sm offcanvas-lg offcanvas-xl');
        // Reset.
        // if (self.isLastOffcanvas) {
            // self.reset();
        // }
    })
    let offcanvasBottom = document.getElementById('offcanvasBottom');
    offcanvasBottom.addEventListener('hide.bs.offcanvas', event => {
        console.log('hide.bs.offcanvas offcanvasBottom');
        const self = MyFolder.offcanvas.load();
        self.next();
    })
    offcanvasBottom.addEventListener('hidden.bs.offcanvas', event => {
        console.log('hidden.bs.offcanvas offcanvasBottom');
        const self = MyFolder.offcanvas.load();
        if (self.pauseToggle) {
            self.pauseToggle = false;
            self.currentOffcanvas.toggle();
        }
        // const self = MyFolder.offcanvas.load();
        // Delete all accesories.
        // $(self.primary._dialog).removeClass('offcanvas-fullscreen offcanvas-sm offcanvas-lg offcanvas-xl');
        // Reset.
        // if (self.isLastOffcanvas) {
            // self.reset();
        // }
    })
}
MyFolder.offcanvas.prototype.reset = function () {
    // console.info('::reset() on fire.');
    this.currentIndex = 0;
    this.currentName = undefined;
    this.registry.byQueue = [];
    this.currentOffcanvas = undefined;
    this.otherOffcanvas = undefined;
    this.offcanvas = undefined;
    this.offcanvasTop = undefined;
    this.offcanvasRight = undefined;
    this.offcanvasBottom = undefined;
    this.pauseToggle = false;
    this.isLastOffcanvas = false;
}
MyFolder.offcanvas.prototype.toggle = function (name) {
    console.info('::toggle() on fire.');
    if (typeof name === 'string') {
        this.reset();
        this.registry.byQueue.push(name);
    }
    let i = this.currentIndex;
    this.currentName = this.registry.byQueue[i];
    let n = this.currentName;
    if (n in this.registry.byName) {
        ref = this.registry.byName[n];
    }
    if (typeof ref !== 'object') {
        console.error('Reference of offcanvas is not object.');
        return;
    }
    let offcanvasOptions = ('bootstrapOptions' in ref) ? ref.bootstrapOptions : {};
    // console.log('this');
    // debug(this);
    // console.log(this);
    let placement = 'start';
    if ('layout' in ref) {
        placement = ('placement' in ref.layout) ? ref.layout.placement : 'start';
    }
    console.log('placement:'+placement);
    if (typeof this.currentOffcanvas === 'object') {
        this.otherOffcanvas = this.currentOffcanvas;
        this.currentOffcanvas = undefined;
    }
    switch (placement) {
        case 'start':
            if (typeof this.offcanvas === 'undefined') {
                this.offcanvas = new bootstrap.Offcanvas('#offcanvas', offcanvasOptions);
            }
            this.currentOffcanvas = this.offcanvas;
            break;
        case 'top':
            if (typeof this.offcanvasTop === 'undefined') {
                this.offcanvasTop = new bootstrap.Offcanvas('#offcanvasTop', offcanvasOptions);
            }
            this.currentOffcanvas = this.offcanvasTop;
            break;
        case 'end':
            if (typeof this.offcanvasRight === 'undefined') {
                this.offcanvasRight = new bootstrap.Offcanvas('#offcanvasRight', offcanvasOptions);
            }
            this.currentOffcanvas = this.offcanvasRight;
            break;
        case 'bottom':
            if (typeof this.offcanvasBottom === 'undefined') {
                this.offcanvasBottom = new bootstrap.Offcanvas('#offcanvasBottom', offcanvasOptions);
            }
            this.currentOffcanvas = this.offcanvasBottom;
            break;
        default:
            console.error('Placement unknown:'+placement);
            break;
    }
    if (typeof this.otherOffcanvas === 'object') {
        let _elementID = this.otherOffcanvas._element.id;
        if (_elementID == 'offcanvas' && placement == 'start') {
            // Beri tanda.
            this.pauseToggle = true;
            return;
        }
        else if (_elementID == 'offcanvasTop' && placement == 'top') {
            // Beri tanda.
            this.pauseToggle = true;
            return;
        }
        else if (_elementID == 'offcanvasRight' && placement == 'end') {
            // Beri tanda.
            this.pauseToggle = true;
            return;
        }
        else if (_elementID == 'offcanvasBottom' && placement == 'bottom') {
            // Beri tanda.
            this.pauseToggle = true;
            return;
        }
    }
    this.currentOffcanvas.toggle();
    return;
    if ('layout' in ref) {
        let size = ('size' in ref.layout) ? ref.layout.size : '';
        let title = ('title' in ref.layout) ? ref.layout.title : '';
        let body = ('body' in ref.layout) ? ref.layout.body : '';
        let footer = ('footer' in ref.layout) ? ref.layout.footer : '';
        this.setSize(size)
            .setTitle(title)
            .setBody(body)
            .setFooter(footer);
        // @todo, gabung aja nih.
        if ('fetch' in ref.layout) {
            MyFolder.fetch(ref.layout.fetch);
        }
        else {
            MyFolder.attachBehaviors(this.currentOffcanvas._element);
        }
        if ('ajax' in ref.layout) {
            MyFolder.ajax.command(this.currentOffcanvas._element, ref.layout.ajax)
        }
    }
}
MyFolder.offcanvas.prototype.next = function () {
    if (this.currentIndex <= (this.registry.byQueue.length - 1)) {
        this.currentIndex++;
    }
    if (this.currentIndex > (this.registry.byQueue.length - 1)) {
        this.isLastOffcanvas = true;
    }
    if (!(this.isLastOffcanvas)) {
        this.toggle();
    }
}
MyFolder.offcanvas.prototype.setSize = function (size) {
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
        case 'Fullscreen':
            var classAdded = 'offcanvas-fullscreen';
            break;
    }
    if (classAdded !== '') {
        $(this.currentOffcanvas._dialog).addClass(classAdded);
    }
    return this;
}
MyFolder.offcanvas.prototype.setTitle = function (title) {
    if (typeof title === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-title').text(title);
    }
    else if (typeof title === 'object' && 'html' in title) {
        $(this.currentOffcanvas._element).find('.offcanvas-title').html(title.html);
    }
    return this;
}
MyFolder.offcanvas.prototype.setBody = function (body) {
    if (typeof body === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-body').empty().text(body);
    }
    else if (typeof body === 'object' && 'html' in body) {
        $(this.currentOffcanvas._element).find('.offcanvas-body').html(body.html);
    }
    return this;
}
MyFolder.offcanvas.prototype.setFooter = function (footer) {
    if (typeof footer === 'string') {
        $(this.currentOffcanvas._element).find('.offcanvas-footer').empty().text(footer);
    }
    else if (typeof footer === 'object' && 'html' in footer) {
        $(this.currentOffcanvas._element).find('.offcanvas-footer').html(footer.html);
    }
    return this;
}