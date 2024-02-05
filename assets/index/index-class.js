/**
 * Index object.
 *
 * @param options
 *
 * @reference
 *   https://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable-string/20732091#20732091
 */
MyFolder.index = function (options) {
    this.cssNth = 1
    // Perlu di set timeout, agar render chunk terlihat
    // user.
    this.chunkSize = 100;
    // 2000 files render dalam 1 detik.
    this.ls_delay = 50;
    // 1000 files render dalam 1 detik.
    // 2000 files render dalam 2 detik.
    this.ls_la_delay = 100;
    this.ls_chunks = [];
    this.ls_la_chunks = [];
    this.drawTable(options);
    this.$table = $('#table-main');
    this.$tbody = this.$table.find('tbody').empty();
}
MyFolder.index.prototype.drawTable = function (info) {
    let root;
    if (typeof info === 'object' && 'root' in info) {
        root = info.root;
    }
    let url = '/index';
    url = MyFolder.pseudoLink(url);
    let ls = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls',
        directory: MyFolder.settings.pathInfo,
        root: root
      }
    });
    let ls_la = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls -la',
        directory: MyFolder.settings.pathInfo,
        root: root
      }
    });
    let that = this;
    this.defer = $.Deferred();
    ls.done(function (data) {
        that.ls_result = data;
        that.drawColumnName().done(function () {
            // Series (not async, not parallel) process draw column other, after main column finished.
            ls_la.done(function (data) {
                that.ls_la_result = data;
                that.drawColumnOther()
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
    var info = {type: '.', name: '', directory: directory+'/'}
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
        url2 += '/'+array[i]
        directory+='/'+array[i]
        var $li = $('<li></li>').addClass('breadcrumb-item');
        var info = {type: '.', name: array[i], directory: directory+'/'}
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
    $this = $(this);
    if ($this.data('infoType') == '.') {
        event.preventDefault();
        var info = $this.data('info')
        if (typeof info.directory !== 'undefined') {
            MyFolder.settings.pathInfo = info.directory
            history.pushState({pathInfo: MyFolder.settings.pathInfo}, "", MyFolder.settings.basePath + info.directory);
        }
        else {
            var name = $this.data('info').name
            MyFolder.settings.pathInfo = MyFolder.settings.pathInfo + name + '/'
            if (MyFolder.settings.rewriteUrl) {
                history.pushState({pathInfo: MyFolder.settings.pathInfo}, "", name + '/');
            }
            else {
                history.pushState({pathInfo: MyFolder.settings.pathInfo}, "", MyFolder.settings.basePath+MyFolder.settings.pathInfo);
            }
        }
        // MyFolder.index.drawTable();
        new MyFolder.index();
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
    const data = this.ls_result
    this.deferColumnName = $.Deferred();
    for (let i = 0; i < data.length; i += this.chunkSize) {
        const chunk = data.slice(i, i + this.chunkSize);
        this.ls_chunks.push(chunk);
    }
    let that = this;
    this.deferColumnName.then(function () {
        that.drawColumnNameChunk();
    });
    this.deferColumnName.resolve();
    return this.defer;
}
MyFolder.index.prototype.drawColumnNameChunk = function() {
    let data;
    data = this.ls_chunks.shift()
    if (data) {
        // Daripada bikin object baru, manfaatin aja object
        // yang kelepas, pasang lagi.
        this.ls_la_chunks.push(data);

        for (i in data) {
            let $tr = $('<tr></tr>').data('infoName',data[i]).html('<th scope="row"></th>').appendTo(this.$tbody);
            let $td = $('<td class="name"></td>').text(data[i]).appendTo($tr);
            $tr.append('<td class="mtime"></td><td class="type"></td><td class="size"></td>');
        }
        //
        let that = this;
        this.deferColumnName.then(function () {
            that.drawColumnNameChunk();
        });
        setTimeout(function () {
            that.deferColumnName.resolve();
        }, this.ls_delay);
    }
    else {
        // Finish draw first column.
        this.defer.resolve();
    }
}
MyFolder.index.prototype.drawColumnOther = function() {
    this.deferColumnOther = $.Deferred();
    let that = this;
    this.deferColumnOther.then(function () {
        console.log('>that.drawColumnOtherChunk();');
        that.drawColumnOtherChunk();
    });
    this.deferColumnOther.resolve();
}
MyFolder.index.prototype.drawColumnOtherChunk = function() {
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
            $tr = $("tr:nth-child("+this.cssNth+")", this.$tbody);
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
        this.deferColumnOther.then(function () {
            console.log('>that.drawColumnOtherChunk();');
            that.drawColumnOtherChunk();
        });
        setTimeout(function () {
            that.deferColumnOther.resolve();
        }, this.ls_la_delay);
    }
}
