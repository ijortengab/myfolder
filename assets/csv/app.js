MyFolder = window.MyFolder || {}

MyFolder.article = MyFolder.article || {}

MyFolder.article.source = $('body article pre').text()

MyFolder.article.element = $('body article').get(0)

MyFolder.article.render = function (source) {
    // @todo, perlu dibuat configurasi.
    let csv_options = {
        separator: ";",
        delimiter: '"'
    }
    let array = $.csv.toArrays(source, csv_options);
    var $table = $('<table class="table table-striped" style="width:100%"></table>');
    var namesType = $.fn.dataTable.absoluteOrderNumber([
        {value: '', position: 'bottom'}
    ]);
    let header = [];
    array.shift().forEach(function (i) {
        header.push({title: i, defaultContent: ''})
    })
    $table.DataTable({
        deferRender: true,
        columns: header,
        data: array,
        paging: false,
        columnDefs: [
            { type: namesType, targets: 0 }
        ],
        fixedHeader: true
    });
    return $table;
}

MyFolder.article.autoPreview = false

$(document).ready(function () {
    // Instead of MyFolder.article.element, we use the original `pre`.
    let $articlePre = $('body article pre');
    let html = MyFolder.article.render(MyFolder.article.source)
    $articlePre.replaceWith(html);
    // Aktifkan fixed header dengan resize event.
    $(document).resize();
})
