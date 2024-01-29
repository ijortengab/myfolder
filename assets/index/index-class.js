/**
 * Index object.
 *
 * @param options
 *
 * @reference
 *   https://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable-string/20732091#20732091
 */
MyFolder.index = function (options) {
    this.drawTable(options);
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
    ls.done(function (data) {
        that.drawColumnName(data).then(function () {
            ls_la.done(function (data) {
                that.drawColumnOther(data)
            })
        })
    })
    this.drawBreadcrumb();
    // $a.before('<i class="bi bi-house-door-fill"></i> ');
    //
    //house-door-fill$a.attr('href', href+'/');
    //
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
        .data('type',info.type)
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
            .data('type',info.type)
            .text(info.name).appendTo($li);
        $li.appendTo($ol);
    }
}
MyFolder.index.prototype.gotoLink = function (event) {
    $this = $(this);
    if ($this.data('type') == '.') {
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
MyFolder.index.prototype.drawColumnName = function(data) {
    var defer = $.Deferred();
    var $table = $('#table-main');
    var $tbody = $table.find('tbody').empty();
    for (i in data) {
        var $tr = $('<tr></tr>').data('info',data[i]).html('<th scope="row"></th>').appendTo($tbody);
        var $td = $('<td></td>').appendTo($tr);
        var href = MyFolder.settings.basePath + MyFolder.settings.pathInfo+data[i];
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .on('click',this.gotoLink)
            .text(data[i]).attr('href',href).appendTo($td);
        $('<td class="mtime"></td><td class="type"></td><td class="size"></td>').appendTo($tr);
    }
    defer.resolve();
    // console.log('sleep 2');
    // setTimeout(function () {
        // defer.resolve();
    // }, 2000);
    return defer;
}
MyFolder.index.prototype.drawColumnOther = function(data) {
    var $table = $('#table-main');
    var $tbody = $table.find('tbody');
    let that = this;
    for (i in data) {
        var info = data[i]
        $tbody.find('tr').filter(function (i) {
            var $this = $(this);
            if ($this.data('info') == info.name) {
                if (info.type == '.') {
                    $this.find("td.type").text('File folder')
                    var $a = $this.find("td > a");
                    $a.before('<i class="bi bi-folder"></i> ');
                    var href = $a.attr('href');
                    $a.attr('href', href+'/');
                    $a.data('info',info);
                    $a.data('type',info.type);
                }
                else {
                    let ms = info.mtime * 1000
                    let d = new Date(ms)
                    $this.find("td.mtime").text(d.format('Y-m-d H:i:s'))
                    $this.find("td.size").text(that.humanFileSize(info.size))
                    var $a = $this.find("td > a");
                    var biclass = that.getClassByType(info.type)
                    if (biclass != '') {
                        $a.before('<i class="'+biclass+'"></i> ');
                    }
                    else {
                        $a.before('<i class="bi bi-filetype-'+info.type+'"></i> ');
                    }
                    $this.find("td.type").text(info.type.toUpperCase()+' File')
                }
                MyFolder.elementModifier($a[0], info)
            }
        });
    }
}
