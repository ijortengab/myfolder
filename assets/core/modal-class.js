/**
 * Modal object.
 *
 * Object wrapper untuk menghandle multiple modal.
 * Untuk mendapatkan object dari class ini, gunakan static function
 * `MyFolder.modal.load()` yang akan me-return property
 * `MyFolder.modal.instance`.
 *
 * @reference
 *   https://getbootstrap.com/docs/5.3/components/modal/#toggle-between-modals
 */
MyFolder.modal = function () {
    this.registry = MyFolder.modal.registry;
    this.currentIndex = 0;
    this.currentName;
    this.currentModal;
    this.primary;
    this.secondary;
    this.isLastModal = false;
    // event listener.
    let primary = document.getElementById('ModalPrimary');
    primary.addEventListener('hide.bs.modal', event => {
        // console.log('hide.bs.modal primary');
        const self = MyFolder.modal.load();
        self.next();
    })
    primary.addEventListener('hidden.bs.modal', event => {
        // console.log('hidden.bs.modal primary');
        const self = MyFolder.modal.load();
        // Delete all accesories.
        $(self.primary._dialog).removeClass('modal-fullscreen modal-sm modal-lg modal-xl');
        // Reset.
        if (self.isLastModal) {
            self.reset();
        }
    })
    let secondary = document.getElementById('ModalSecondary');
    secondary.addEventListener('hide.bs.modal', event => {
        // console.log('hide.bs.modal secondary');
        const self = MyFolder.modal.load();
        self.next();
    })
    secondary.addEventListener('hidden.bs.modal', event => {
        // console.log('hidden.bs.modal secondary');
        const self = MyFolder.modal.load();
        // Delete all accesories.
        $(self.secondary._dialog).removeClass('modal-fullscreen modal-sm modal-lg modal-xl');
        // Reset.
        if (self.isLastModal) {
            self.reset();
        }
    })
}
MyFolder.modal.prototype.reset = function () {
    // console.info('::reset() on fire.');
    this.currentIndex = 0;
    this.currentName = undefined;
    this.registry.byQueue = [];
    this.currentModal = undefined;
    this.primary = undefined;
    this.secondary = undefined;
    this.isLastModal = false;
}
MyFolder.modal.prototype.toggle = function (name) {
    // console.info('::toggle() on fire.');
    if (typeof name === 'string') {
        this.reset();
        this.registry.byQueue.push(name);
    }
    if (this.registry.byQueue.length === 0) {
        return;
    }
    let i = this.currentIndex;
    this.currentName = this.registry.byQueue[i];
    let n = this.currentName;
    if (n in this.registry.byName) {
        ref = this.registry.byName[n];
    }
    if (typeof ref === 'undefined') {
        console.error('Reference of modal is not object.');
        return;
    }
    let modalOptions = ('bootstrapOptions' in ref) ? ref.bootstrapOptions : {};
    if (typeof this.primary === 'undefined') {
        this.primary = new bootstrap.Modal('#ModalPrimary', modalOptions);
        this.currentModal = this.primary;
    }
    else if (typeof this.secondary === 'undefined') {
        this.secondary = new bootstrap.Modal('#ModalSecondary', modalOptions);
        this.currentModal = this.secondary;
    }
    else if (this.currentModal._element.id == 'ModalSecondary') {
        this.currentModal = this.primary;
    }
    else {
        this.currentModal = this.secondary;
    }
    if ('layout' in ref) {
        let size = ('size' in ref.layout) ? ref.layout.size : '';
        let title = ('title' in ref.layout) ? ref.layout.title : '';
        let body = ('body' in ref.layout) ? ref.layout.body : '';
        let footer = ('footer' in ref.layout) ? ref.layout.footer : '';
        this.setTitle(title)
            .setBody(body)
            .setFooter(footer)
            .setSize(size);
        if ('fetch' in ref.layout) {
            let base = this.currentModal._element.id || 'blank';
            let url = MyFolder.pseudoLink(ref.layout.fetch);
            MyFolder.ajax[base] = new MyFolder.ajax(base, this.currentModal._element, {url: url});
        }
        else {
            MyFolder.attachBehaviors(this.currentModal._element);
        }
    }
    this.currentModal.toggle();
}
MyFolder.modal.prototype.next = function () {
    if (this.currentIndex <= (this.registry.byQueue.length - 1)) {
        this.currentIndex++;
    }
    if (this.currentIndex > (this.registry.byQueue.length - 1)) {
        this.isLastModal = true;
    }
    if (!(this.isLastModal)) {
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
