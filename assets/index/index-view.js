MyFolder = window.MyFolder || {}

MyFolder.index.view = {
    $list: $('#index-view').find('[data-view=list]'),
    $details: $('#index-view').find('[data-view=details]')
}

MyFolder.index.view.$list.on('click', function (e) {
    e.preventDefault();
    localStorage.setItem("indexView", "list");
    const index = MyFolder.index.instance
    index.draw_start = Date.now();
    index.ls_start = Date.now();
    index.ls_la_start = Date.now();
    index.resetTable()
    index.defer = $.Deferred();
    index.drawColumnName().drawColumnOther().done(function () {
        // Finish draw table.
        const draw_start = index.draw_start;
        const draw_end = Date.now();
        console.log(`Execution time of drawTable: ${draw_end - draw_start} ms`);
    });
});

MyFolder.index.view.$details.on('click', function (e) {
    e.preventDefault();
    localStorage.setItem("indexView", "details");
    const index = MyFolder.index.instance
    index.draw_start = Date.now();
    index.ls_start = Date.now();
    index.ls_la_start = Date.now();
    index.resetTable()
    index.defer = $.Deferred();
    index.drawColumnName().drawColumnOther().done(function () {
        // Finish draw table.
        const draw_start = index.draw_start;
        const draw_end = Date.now();
        console.log(`Execution time of drawTable: ${draw_end - draw_start} ms`);
    });
});
