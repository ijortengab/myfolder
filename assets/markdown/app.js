MyFolder = window.MyFolder || {}

MyFolder.markdown = markdownit({
    html: true,
    linkify: true,
    typography: true
})
.use(markdownitSup)
.use(markdownitTaskLists)
.use(markdownItAnchor)
.use(markdownitEmoji)
// Dibutuhkan options.html5embed atau error.
.use(markdownitHTML5Embed, { html5embed: {attributes: {video: 'controls preload="metadata" class="video-js" data-setup="{}" '}}})

MyFolder.parseYaml = function (string) {
    let SexyYamlType = new jsyaml.Type('!sexy', {
      // See node kinds in YAML spec: http://www.yaml.org/spec/1.2/spec.html#kind//
      kind: 'sequence',
      construct: function (data) {
        return data.map(function (string) { return 'sexy ' + string; });
      }
    });
    let SEXY_SCHEMA = jsyaml.DEFAULT_SCHEMA.extend([ SexyYamlType ]);
    data = jsyaml.load(string, { schema: SEXY_SCHEMA });
    return data;
}

// Populate MyFolder.article agar bisa dioprek oleh module lainnya.
// Standard:
// MyFolder.article = {source:, STRING, element: ELEMENT, render = FUNCTION }
MyFolder.article = MyFolder.article || {}

MyFolder.article.source = $('body article pre').text()

MyFolder.article.element = $('body article').get(0)

MyFolder.article.render= function (source) {
    let matches;
    let markdown;
    let front_matter;
    let $code = $('body pre code');
    matches = /^(-{3}(?:\n|\r)([\w\W]+?)(?:\n|\r)-{3})?([\w\W]*)*/.exec(source)
    if (matches[1]) {
        front_matter = matches[2];
        markdown = matches[3];
    }
    else {
        markdown = matches[0];
    }
    if (front_matter) {
        // Jika sebelumnya tidak ada, maka
        if ($code.length === 0) {
            $code = $('<pre><code></code></pre>').prependTo('body').children();
        }
        $code.text(front_matter);
        let json = MyFolder.parseYaml(front_matter);
        console.log(json);
    }
    return MyFolder.markdown.render(markdown);
}

$(document).ready(function () {
    // Instead of MyFolder.article.element, we use the original `pre`.
    let $articlePre = $('body article pre');
    let html = MyFolder.article.render(MyFolder.article.source)
    $articlePre.replaceWith(html);
})
