// Object wrapper untuk menghandle multiple modal.
// Reference: https://getbootstrap.com/docs/5.3/components/modal/#toggle-between-modals
MyFolder.modal = function () {
    this.registry = MyFolder.modal.registry;
    this.currentIndex = 0;
    this.currentModal;
    this.otherModal;
    this.primary;
    this.secondary;
    this.scripts = [];
    this.isLastModal = false;
}
MyFolder.modal.prototype.reset = function () {
    this.currentIndex = 0;
    this.registry.byQueue = [];
    this.registry.lastIndexShown = -1;
    this.currentModal = undefined;
    this.otherModal = undefined;
    this.primary = undefined;
    this.secondary = undefined;
    this.isLastModal = false;
}
MyFolder.modal.prototype.toggle = function (name) {
    if (this.currentIndex >= (this.registry.byQueue.length - 1)) {
        this.isLastModal = true;
    }
    const self = this;
    if (typeof name !== 'undefined') {
        console.log('>this.reset();');
        this.reset();
        console.log('>this.registry.byQueue.push(name);');
        this.registry.byQueue.push(name);
    }
    let i = this.currentIndex
    console.log('i');
    console.log(i);
    let ref = this.registry.byQueue[i]
    if (typeof ref == 'string') {
        ref = this.registry.byName[ref]
    }
    if (typeof ref == 'undefined') {
        return;
    }
    let modalOptions = ('bootstrapOptions' in ref) ? ref.bootstrapOptions : {};
    if (typeof this.primary === 'undefined') {
        this.preparePrimary();
        this.primary = new bootstrap.Modal('#ModalPrimary', modalOptions);
        this.currentModal = this.primary;
    }
    else if (typeof this.secondary === 'undefined') {
        this.secondary = new bootstrap.Modal('#ModalSecondary', modalOptions);
        this.prepareSecondary();
        this.currentModal = this.secondary;
        this.otherModal = this.primary;
    }
    else if (this.currentModal._element.id == 'ModalPrimary') {
        this.currentModal = this.secondary;
        this.otherModal = this.primary;
    }
    else {
        this.currentModal = this.primary;
        this.otherModal = this.secondary;
    }
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
            MyFolder.attachBehaviors(this.currentModal._element);
        }
        if ('ajax' in ref.layout) {
            MyFolder.ajax.command(this.currentModal._element, ref.layout.ajax)
        }
    }
    const myModalEl = this.currentModal._element
    myModalEl.addEventListener('shown.bs.modal', event => {
        let object = MyFolder.modal.load();
        MyFolder.modal.registry.lastIndexShown = object.currentIndex
    })
    myModalEl.addEventListener('hidden.bs.modal', event => {
        let that = MyFolder.modal.load();
        // Delete all accesories.
        $(that.currentModal._dialog).removeClass('modal-fullscreen modal-sm modal-lg modal-xl');
        if (that.isLastModal) {
            that.reset();
        }
    })
    this.currentModal.toggle();
    if (typeof this.otherModal !== 'undefined') {
        this.otherModal.toggle();
    }
}
MyFolder.modal.prototype.next = function () {
    if (this.currentIndex < (this.registry.byQueue.length - 1)) {
        this.currentIndex++;
        this.toggle();
    }
}
MyFolder.modal.prototype.setSize = function (size) {
    switch (size) {
        case 'Small':
            var classAdded = 'modal-sm';
            break;
        case 'Large':
            var classAdded = 'modal-lg';
            break;
        case 'Extra large':
            var classAdded = 'modal-xl';
            break;
        case 'Fullscreen':
            var classAdded = 'modal-fullscreen';
            break;
    }
    if (classAdded !== '') {
        $(this.currentModal._dialog).addClass(classAdded);
    }
    return this;
}
MyFolder.modal.prototype.setTitle = function (title) {
    if (typeof title === 'string') {
        $(this.currentModal._element).find('.modal-title').text(title);
    }
    else if (typeof title === 'object' && 'html' in title) {
        $(this.currentModal._element).find('.modal-title').html(title.html);
    }
    return this;
}
MyFolder.modal.prototype.setBody = function (body) {
    if (typeof body === 'string') {
        $(this.currentModal._element).find('.modal-body').empty().text(body);
    }
    else if (typeof body === 'object' && 'html' in body) {
        $(this.currentModal._element).find('.modal-body').html(body.html);
    }
    return this;
}
MyFolder.modal.prototype.setFooter = function (footer) {
    if (typeof footer === 'string') {
        $(this.currentModal._element).find('.modal-footer').empty().text(footer);
    }
    else if (typeof footer === 'object' && 'html' in footer) {
        $(this.currentModal._element).find('.modal-footer').html(footer.html);
    }
    return this;
}
MyFolder.modal.prototype.preparePrimary = function (element) {
    const self = this
    $('#ModalPrimary .modal-header button').on('click',function (e) {
        console.log('self.isLastModal',JSON.stringify(self.isLastModal));
        if (!(self.isLastModal)) {
            self.next();
        }
    });
}
MyFolder.modal.prototype.prepareSecondary = function (element) {
    const self = this
    $('#ModalSecondary .modal-header button').on('click',function (e) {
        console.log('self.isLastModal',JSON.stringify(self.isLastModal));
        if (!(self.isLastModal)) {
            self.next();
        }
    });
}
