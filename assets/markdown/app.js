// Source: https://cdn.jsdelivr.net/npm/@vrcd-community/markdown-it-video@1.1.1/index.js
var $articlePre = $('body article pre');
var $code = $('body pre code');
var article = $articlePre.text();
var code = $code.text();

var md = markdownit({
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

var SexyYamlType = new jsyaml.Type('!sexy', {
  // See node kinds in YAML spec: http://www.yaml.org/spec/1.2/spec.html#kind//
  kind: 'sequence',
  construct: function (data) {
    return data.map(function (string) { return 'sexy ' + string; });
  }
});
var SEXY_SCHEMA = jsyaml.DEFAULT_SCHEMA.extend([ SexyYamlType ]);
data = jsyaml.load(code, { schema: SEXY_SCHEMA });
$articlePre.replaceWith(md.render(article));
console.log(code);
console.log(data);
