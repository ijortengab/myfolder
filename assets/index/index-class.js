/**
 * Index object.
 *
 * @param options
 *
 * @reference
 *   https://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable-string/20732091#20732091
 */
MyFolder.index = function (options) {
    // Perlu di set timeout, agar render chunk terlihat
    // user.
    this.draw_start;
    this.ls_start;
    this.ls_la_start;
    this.ls_chunk_size = 50;
    // Harus lebih kecil.
    // ls_la_chunk_size < ls_chunk_size.
    this.ls_la_chunk_size = 25;
    // this.ls_delay = 100;
    // this.ls_la_delay = 200;
    this.ls_chunks = [];
    this.ls_la_chunks = [];
    this.$table = $('#table-main');
    this.$theadtr = this.$table.find('thead tr');
    this.$tbody = this.$table.find('tbody');

    // Property indexView.
    this.indexView = 'details';
    // Ambil informasi indexView dari server terlebih dahulu, baru kemudian di-
    // override dari local storage.
    if (typeof options === 'object' && 'indexView' in options) {
        this.indexView = options.indexView;
    }

    // Jika user menge-click breadcrumb, sementara proses drawing masih
    // berjalan.
    this.cancelDrawing = false
    // Lets drawing;
    this.drawTable(options);
}
MyFolder.index.prototype.resetTable = function () {
    this.cssNth = 1
    let indexView = localStorage.getItem("indexView");
    if (indexView != null) {
        this.indexView = indexView;
    }
    // Rebuild heading and body.
    this.$theadtr.empty();
    this.$tbody.empty();

    // Rebuild Heading.
    $('<th scope="col"></th>').attr('data-field', 'id').text('#').appendTo(this.$theadtr);
    $('<th scope="col"></th>').attr('data-field', 'name').text('Name').appendTo(this.$theadtr);

    if (this.indexView == 'details') {
        $('<th scope="col"></th>').attr('data-field', 'date-modified').text('Date Modified').appendTo(this.$theadtr);
        $('<th scope="col"></th>').attr('data-field', 'date-type').text('Type').appendTo(this.$theadtr);
        $('<th scope="col"></th>').attr('data-field', 'date-size').text('Size').appendTo(this.$theadtr);
    }
}
MyFolder.index.prototype.drawTable = function (options) {
    this.resetTable();
    this.draw_start = Date.now();
    let root;
    if (typeof options === 'object' && 'root' in options) {
        root = options.root;
    }
    let url = '/index';
    url = MyFolder.pseudoLink(url);
    let ls = $.ajax({
      type: "POST",
      url: url,
      dataType: "json",
      data: {
        action: 'ls',
        directory: MyFolder.settings.pathInfo,
        root: root
      }
    });
    let ls_la = $.ajax({
      type: "POST",
      url: url,
      dataType: "json",
      data: {
        action: 'ls -la',
        directory: MyFolder.settings.pathInfo,
        root: root
      }
    });
    let that = this;
    this.defer = $.Deferred();
    ls.done(function (data) {
        // console.log('ls done.');
        that.ls_start = Date.now();
        that.ls_result = data;
        that.drawColumnName()
        // Selesai load metadata.
        ls_la.done(function (data) {
            // console.log('ls -la done.');
            that.ls_la_start = Date.now();
            that.ls_la_result = data;
            that.drawColumnOther().done(function () {
                // Finish draw table.
                const draw_start = that.draw_start;
                const draw_end = Date.now();
                console.log(`Execution time of drawTable: ${draw_end - draw_start} ms`);
            })
        })
    })
    this.drawBreadcrumb();
}
MyFolder.index.prototype.drawBreadcrumb = function () {
    var array = MyFolder.settings.pathInfo.split('/').slice(1,-1);
    var $ol = $('ol.breadcrumb').empty();
    var $li = $('<li></li>').addClass('breadcrumb-item');
    let url2 = MyFolder.settings.basePath;
    var directory = '';
    var directoryEncoded = '';
    var info = {type: '.', name: '', directory: directory+'/', directoryEncoded: directoryEncoded+'/'}
    var $a = $('<a></a>')
        .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
        .attr('href',url2+'/')
        .on('click',this.gotoLink)
        .data('info',info)
        .data('infoType',info.type)
        .text(info.name).appendTo($li);
    $('<i class="bi bi-house-door"></i>').appendTo($a);
    $li.appendTo($ol);
    for (i in array) {
        url2 += '/'+encodeURIComponent(array[i])
        directory+='/'+array[i]
        directoryEncoded+='/'+encodeURIComponent(array[i])
        var $li = $('<li></li>').addClass('breadcrumb-item');
        var info = {type: '.', name: array[i], directory: directory+'/', directoryEncoded: directoryEncoded+'/'}
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .attr('href',url2+'/')
            .on('click',this.gotoLink)
            .data('info',info)
            .data('infoType',info.type)
            .text(info.name).appendTo($li);
        $li.appendTo($ol);
    }
}
MyFolder.index.prototype.gotoLink = function (event) {
    // Interruption if any.
    MyFolder.index.instance.cancelDrawing = true;
    $this = $(this);
    if ($this.data('infoType') == '.') {
        event.preventDefault();
        var info = $this.data('info')
        if (typeof info.directory !== 'undefined') {
            // Draw breadcrumb.
            MyFolder.settings.pathInfo = info.directory
            MyFolder.settings.pathInfoEncoded = info.directoryEncoded
            let state = {pathInfo: MyFolder.settings.pathInfo, pathInfoEncoded: MyFolder.settings.pathInfoEncoded}
            window.history.pushState(state, "", MyFolder.settings.basePath + info.directoryEncoded);
        }
        else {
            var name = $this.data('info').name
            MyFolder.settings.pathInfo = MyFolder.settings.pathInfo + name + '/'
            MyFolder.settings.pathInfoEncoded = MyFolder.settings.pathInfoEncoded + encodeURIComponent(name) + '/'
            let state = {pathInfo: MyFolder.settings.pathInfo, pathInfoEncoded: MyFolder.settings.pathInfoEncoded}
            if (MyFolder.settings.rewriteUrl) {
                window.history.pushState(state, "", encodeURIComponent(name) + '/');
            }
            else {
                window.history.pushState(state, "", MyFolder.settings.basePath + MyFolder.settings.pathInfoEncoded);
            }
        }
        // MyFolder.index.drawTable();
        MyFolder.index.instance = new MyFolder.index();
    }
    else {
        // Bugfix. Kembalikan ke false. User yang menge-click file, maka
        // info cancelDrawing perlu dikembalikan.
        MyFolder.index.instance.cancelDrawing = false;
    }
}
MyFolder.index.prototype.getClassByType = function (type) {
    switch (type) {
        case 'sh':
        case 'gitignore':
            return 'bi bi-file-earmark-code'
        case 'md':
            return 'bi bi-file-earmark-richtext'
        default:
            return 'bi bi-file-earmark-text'
    }
}
MyFolder.index.prototype.humanFileSize = function(size) {
    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}
MyFolder.index.prototype.drawColumnName = function() {
    if (this.cancelDrawing) {
        return;
    }
    const data = this.ls_result
    this.deferColumnName = $.Deferred();
    for (let i = 0; i < data.length; i += this.ls_chunk_size) {
        const chunk = data.slice(i, i + this.ls_chunk_size);
        this.ls_chunks.push(chunk);
    }

    let that = this;
    this.deferColumnName.then(function () {
        that.drawColumnNameChunk();
    });
    this.deferColumnName.resolve();
    return this;
}
MyFolder.index.prototype.drawColumnNameChunk = function() {
    if (this.cancelDrawing) {
        return;
    }
    let data;
    data = this.ls_chunks.shift()
    if (data) {
        for (i in data) {
            let $tr = $('<tr></tr>').data('infoName',data[i]).html('<th scope="row"></th>').appendTo(this.$tbody);
            let $td = $('<td class="name"></td>').text(data[i]).appendTo($tr);
            if (this.indexView == 'details') {
                $tr.append('<td class="mtime"></td><td class="type"></td><td class="size"></td>');
            }
        }
        let that = this;
        this.deferColumnName = $.Deferred();
        this.deferColumnName.then(function () {
            // console.log('>that.drawColumnNameChunk();');
            that.drawColumnNameChunk();
        });
        // console.log('>setTimeout('+this.ls_delay+')');
        // setTimeout(function () {
            // console.log('>that.deferColumnName.resolve();');
            that.deferColumnName.resolve();
        // }, this.ls_delay);
    }
    else {
        // Finish draw first column.
        const ls_start = this.ls_start;
        const ls_end = Date.now();
        console.log(`Execution time of drawColumnName: ${ls_end - ls_start} ms`);
    }
}
MyFolder.index.prototype.drawColumnOther = function() {
    if (this.cancelDrawing) {
        return;
    }
    const data = this.ls_result
    this.deferColumnName = $.Deferred();
    for (let i = 0; i < data.length; i += this.ls_la_chunk_size) {
        const chunk = data.slice(i, i + this.ls_la_chunk_size);
        this.ls_la_chunks.push(chunk);
    }
    this.deferColumnOther = $.Deferred();
    let that = this;
    this.deferColumnOther.then(function () {
        // console.log('>that.drawColumnOtherChunk();');
        that.drawColumnOtherChunk();
    });
    this.deferColumnOther.resolve();
    return this.defer;
}
MyFolder.index.prototype.drawColumnOtherChunk = function() {
    if (this.cancelDrawing) {
        return;
    }
    const details = this.ls_la_result
    let data;
    data = this.ls_la_chunks.shift()
    if (data) {
        for (i in data) {
            find = data[i];
            let info = details.find((object) => {
                if (object.name == find) {
                    return object;
                }
            })
            // Jangan gunakan find(), gunakan nth child.
            // Karena secara paralel, method drawColumnNameChunk juga sedang
            // menggambar tr.
            let $tr = $("tr:nth-child("+this.cssNth+")", this.$tbody);
            // Make sure, jika ada kekacauan karena insert row dadakan oleh
            // module lainnya, maka draw details akan gagal.
            if ($tr.data('infoName') == info.name) {
                let $tdName = $tr.find("td.name").empty();
                let $a = $('<a></a>')
                    .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
                    .on('click',this.gotoLink)
                    .text(info.name).appendTo($tdName);
                if (info.type == '.') {
                    $tr.find("td.type").text('File folder');
                    $a.before('<i class="bi bi-folder"></i> ');
                    $a.data('info',info);
                    $a.data('infoType',info.type);
                }
                else {
                    let ms = info.mtime * 1000
                    let d = new Date(ms)
                    $tr.find("td.mtime").text(d.format('Y-m-d H:i:s'))
                    $tr.find("td.size").text(this.humanFileSize(info.size))
                    var biclass = this.getClassByType(info.type)
                    if (biclass != '') {
                        $a.before('<i class="'+biclass+'"></i> ');
                    }
                    else {
                        $a.before('<i class="bi bi-filetype-'+info.type+'"></i> ');
                    }
                    $tr.find("td.type").text(info.type.toUpperCase()+' File')
                }
                MyFolder.elementModifier($a[0], info)
            }
            this.cssNth++;
        }
        let that = this;
        this.deferColumnOther = $.Deferred();
        this.deferColumnOther.then(function () {
            // console.log('>that.drawColumnOtherChunk();');
            that.drawColumnOtherChunk();
        });
        // console.log('>setTimeout('+this.ls_la_delay+')');
        // setTimeout(function () {
            // console.log('>that.deferColumnOther.resolve();');
            that.deferColumnOther.resolve();
        // }, this.ls_la_delay);
    }
    else {
        // Finish draw other column.
        const ls_la_start = this.ls_la_start;
        const ls_la_end = Date.now();
        console.log(`Execution time of drawColumnOther: ${ls_la_end - ls_la_start} ms`);
        this.defer.resolve();
    }
}
