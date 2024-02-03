MyFolder = window.MyFolder || {}

// Standard:
// MyFolder.article = {source:, STRING, element: ELEMENT, render = FUNCTION }
MyFolder.article = MyFolder.article || {}

MyFolder.ctrlE = function () {
    // Start.
    this.isDialogHide = true;
    this.goRender = false;
    this.confirmSave = false;
    this.timeout = undefined;
    // Prepend element.
    this.$dialog = $([
        '<dialog id="ctrl-e">',
        '<header>Press <kbd>Esc</kbd> to close or save.</header>',
        '<form method="post" enctype="multipart/form-data">',
        '<textarea name="contents" rows="25" cols="80">',
        '</textarea>',
        '</form>',
        '</dialog>',
        '<dialog id="ctrl-s">',
        '<header>Press <kbd>Esc</kbd> to close.</header>',
        '<form method="dialog">',
        '<p>Save the changes?</p>',
        '<menu>',
        '<button value="no">No</button>',
        '<button value="yes">Yes</button>',
        '</menu>',
        '</form>',
        '</dialog>',
        '',
    ].join('')).prependTo('body');
    this.$textarea = this.$dialog.find('textarea');
    this.$form = this.$textarea.parent();
    // Place instance to variable.
    let that = this;
    // Listen event keydown of document.
    document.addEventListener('keydown', e => {
        if (e.ctrlKey && e.key === 'e') {
            // Prevent the Browser behaviour.
            e.preventDefault();
            this.$dialog[0].showModal()
            this.isDialogHide = false;
        }
        else if (e.ctrlKey && e.key === 's') {
            // Prevent the Browser behaviour.
            e.preventDefault();
            // console.log(that);
            let source = MyFolder.article.source;
            let userinput = this.$textarea.val();
            if (source !== userinput) {
                this.save()
            }
            this.confirmSave = false;
            // this.$dialog[0].showModal()
            // this.isDialogHide = false;
        }
    });

    if (typeof MyFolder.article.source === 'undefined') {
        // Load contents.
        let options = {}
        let url = new URL(location.href)
        // Clear the ?html
        url.searchParams.delete('html')
        const promise = fetch(url, options)
        .then(function(response){
            // Convert object response to plaintext.
            return response.text()
        })
        .then(function (result) {
            MyFolder.article.source = result;
            that.$textarea.text(MyFolder.article.source);
        })
        .catch(function(error){
            console.error(error)
        });
    }
    else {
        this.$textarea.text(MyFolder.article.source);
    }

    // Listen event change of textarea.
    this.$textarea.on('change update keyup', function () {
        that.goRender = true;
        // Check
        if (that.timeout) {
            clearTimeout(that.timeout);
        }
        if (!that.isDialogHide) {
            that.timeout = setTimeout(function () {
                that.render()
            }, 1500);
        }
    })
    // Jika di close, maka clear timeout dan segera render.
    this.$dialog[0].addEventListener("close", (event) => {
        clearTimeout(this.timeout);
        this.render();
        this.isDialogHide = true;
        if (this.confirmSave) {
            this.$dialog[1].showModal()
            this.$dialog.find('button[value=yes]').focus()
            this.confirmSave = false;
        }
    });
    // Jika di close, maka clear timeout dan segera render.
    this.$dialog[1].addEventListener("close", (event) => {
        if (event.target.returnValue == 'yes') {
            this.save();
        }
    });

    // Listen form submit.
    this.$form.submit(function (e) {
        // Prevent the Browser behaviour.
        e.preventDefault();
        // var actionUrl = $(this).attr('action');
        // console.log(actionUrl);
        $.ajax({
            type: "POST",
            // url: actionUrl,
            data: $(this).serialize(), // serializes the form's elements.
            success: function(data)
            {
                MyFolder.article.source = that.$textarea.val()
                // Ubah
              // alert(data); // show response from the php script.
            }
        });

    })
}

MyFolder.ctrlE.prototype.render = function () {
    if (this.goRender) {
        // jika tidak ada, beri warning.
        if (MyFolder.article) {
            let source = MyFolder.article.source;
            let userinput = this.$textarea.val();
            if (source !== userinput) {
                this.confirmSave = true;
            }
            if (MyFolder.article.render) {
                let element = MyFolder.article.element;
                let html = MyFolder.article.render(userinput);
                $(element).html(html);
            }
        }
        this.goRender = false;
    }
}

MyFolder.ctrlE.prototype.save = function () {
    this.$form.submit();
}

$(document).ready(function () {
    MyFolder.ctrlE.instance = new MyFolder.ctrlE;
})
