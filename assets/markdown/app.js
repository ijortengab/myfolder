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
.use(markdownitReplaceLink, {
    replaceLink: function (link, env, token, htmlToken) {
        if (link.startsWith('/')) {
            return window.settings.basePath + link;
        }
        else {
            return link;
        }
    }
})
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
// MyFolder.article = {source: STRING, element: DOM, render = FUNCTION, autoPreview: BOOLEAN}
MyFolder.article = MyFolder.article || {}

MyFolder.article.source = $('body article pre').text()

MyFolder.article.element = $('body article').get(0)

MyFolder.article.render= function (source) {
    let matches;
    let markdown;
    let front_matter;
    let $code = $('body pre code.front-matter');
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
            $code = $('<pre><code class="front-matter"></code></pre>').prependTo('body').children();
        }
        $code.parent().show();
        $code.text(front_matter);
        let json = MyFolder.parseYaml(front_matter);
        console.log(json);
    }
    else {
        if ($code.length === 0) {
            $code = $('<pre><code class="front-matter"></code></pre>').prependTo('body').children();
        }
        $code.text('');
        $code.parent().hide();
    }
    let markdownArray = [];
    do {
        matches = /`{3}csv(?:\n|\r)([\w\W]+?)(?:\n|\r)`{3}/.exec(markdown)
        if (matches) {
            markdownArray.push({string: markdown.substring(0,matches.index)})
            markdownArray.push({csv: matches[1]})
            markdown = markdown.substring(matches.index + matches[0].length);
            matches = /`{3}csv(?:\n|\r)([\w\W]+?)(?:\n|\r)`{3}/.exec(markdown)
        }
    }
    while (matches);
    if (markdownArray.length) {
        markdownArray.push({string: markdown})
        markdown = '';
        markdownArray.forEach(function (e) {
            if (e.csv) {
                markdown += csvToMarkdown(e.csv, ";", true)
            }
            else {
                markdown += e.string;
            }
        });
    }
    return MyFolder.markdown.render(markdown);
}

$(document).ready(function () {
    // Instead of MyFolder.article.element, we use the original `pre`.
    let $articlePre = $('body article pre');
    let html = MyFolder.article.render(MyFolder.article.source)
    $articlePre.replaceWith(html);
})
