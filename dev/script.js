console.log(MyFolder);
url=MyFolder.config.base_path+MyFolder.config.path_info
function gotoLink(event) {
    $this = $(this);
    if ($this.data('type') == '.') {
        event.preventDefault();
        var info = $this.data('info')
        if (typeof info.directory !== 'undefined') {
            MyFolder.config.path_info = info.directory
            history.pushState({path_info: MyFolder.config.path_info}, "", MyFolder.config.base_path + info.directory);
        }
        else {
            var name = $this.data('info').name
            MyFolder.config.path_info = MyFolder.config.path_info + name + '/'
            history.pushState({path_info: MyFolder.config.path_info}, "", name + '/');
        }
        refreshDirectory();
    }
}
function getClassByType(type) {
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
function drawColumnName(data) {
    console.log('drawColumnName()');
    var defer = $.Deferred();
    var $table = $('#table-main');
    var $tbody = $table.find('tbody').empty();
    for (i in data) {
        var $tr = $('<tr></tr>').data('info',data[i]).html('<th scope="row"></th>').appendTo($tbody);
        var $td = $('<td></td>').appendTo($tr);
        var href = MyFolder.config.base_path + MyFolder.config.path_info+data[i];
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .on('click',gotoLink)
            .text(data[i]).attr('href',href).appendTo($td);
        $('<td class="mtime"></td><td class="type"></td><td class="size"></td>').appendTo($tr);
    }
    // console.log('sleep 2');
    // setTimeout(function () {
        defer.resolve();
    // }, 2000);
    return defer;
}
// Credit:
// https://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable-string/20732091#20732091
function humanFileSize(size) {
    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}
// Credit:
// - https://gist.github.com/kubiqsk/c60207a3075104df7cc1822a95053ecd
(function(){
	var replaceChars = {
		// day
		d: function(){ return ( '0' + this.getDate() ).slice(-2) },
		D: function( locale ){ return new Intl.DateTimeFormat( locale, { weekday: 'short' } ).format( this ) },
		j: function(){ return this.getDate() },
		l: function( locale ){ return new Intl.DateTimeFormat( locale, { weekday: 'long' } ).format( this ) },
		N: function(){
			let day = this.getDay();
			return day === 0 ? 7 : day;
		},
		S: function(){
			let date = this.getDate();
			return date % 10 === 1 && date !== 11 ? 'st' : ( date % 10 === 2 && date !== 12 ? 'nd' : ( date % 10 === 3 && date !== 13 ? 'rd' : 'th' ) );
		},
		w: function(){ return this.getDay() },
		z: function(){ return Math.floor( ( this - new Date( this.getFullYear(), 0, 1 ) ) / 86400000 ) },
		// week
		W: function(){
			let target = new Date( this.valueOf() );
			let dayNr = ( this.getDay() + 6 ) % 7;
			target.setDate( target.getDate() - dayNr + 3 );
			let firstThursday = target.valueOf();
			target.setMonth( 0, 1 );
			if( target.getDay() !== 4 ){
				target.setMonth( 0, 1 + ( ( 4 - target.getDay() ) + 7 ) % 7 );
			}
			return Math.ceil( ( firstThursday - target ) / 604800000 ) + 1;
		},
		// month
		F: function( locale ){ return new Intl.DateTimeFormat( locale, { month: 'long' } ).format( this ) },
		m: function(){ return ( '0' + ( this.getMonth() + 1 ) ).slice(-2) },
		M: function( locale ){ return new Intl.DateTimeFormat( locale, { month: 'short' } ).format( this ) },
		n: function(){ return this.getMonth() + 1 },
		t: function(){
			let year = this.getFullYear();
			let nextMonth = this.getMonth() + 1;
			if( nextMonth === 12 ){
				year = year++;
				nextMonth = 0;
			}
			return new Date( year, nextMonth, 0 ).getDate();
		},
		// year
		L: function(){
			let year = this.getFullYear();
			return year % 400 === 0 || ( year % 100 !== 0 && year % 4 === 0 ) ? 1 : 0;
		},
		o: function(){
			let date = new Date( this.valueOf() );
			date.setDate( date.getDate() - ( ( this.getDay() + 6 ) % 7 ) + 3 );
			return date.getFullYear();
		},
		Y: function(){ return this.getFullYear() },
		y: function(){ return ( '' + this.getFullYear() ).slice(-2) },
		// time
		a: function(){ return this.getHours() < 12 ? 'am' : 'pm' },
		A: function(){ return this.getHours() < 12 ? 'AM' : 'PM' },
		B: function(){
			return ( '00' + Math.floor( ( ( ( this.getUTCHours() + 1 ) % 24 ) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600 ) * 1000 / 24 ) ).slice(-3);
		},
		g: function(){ return this.getHours() % 12 || 12 },
		G: function(){ return this.getHours() },
		h: function(){ return ( '0' + ( this.getHours() % 12 || 12 ) ).slice(-2) },
		H: function(){ return ( '0' + this.getHours() ).slice(-2) },
		i: function(){ return ( '0' + this.getMinutes() ).slice(-2) },
		s: function(){ return ( '0' + this.getSeconds() ).slice(-2) },
		v: function(){ return ( '00' + this.getMilliseconds() ).slice(-3) },
		// Timezone
		e: function(){ return Intl.DateTimeFormat().resolvedOptions().timeZone },
		I: function(){
			let DST = null;
			for( let i = 0; i < 12; ++i ){
				let d = new Date( this.getFullYear(), i, 1 );
				let offset = d.getTimezoneOffset();
				if( DST === null ){
					DST = offset;
				}else if( offset < DST ){
					DST = offset;
					break;
				}else if( offset > DST ){
					break;
				}
			}
			return ( this.getTimezoneOffset() === DST ) | 0;
		},
		O: function(){
			let timezoneOffset = this.getTimezoneOffset();
			return ( -timezoneOffset < 0 ? '-' : '+' ) + ( '0' + Math.floor( Math.abs( timezoneOffset / 60 ) ) ).slice(-2) + ( '0' + Math.abs( timezoneOffset % 60 ) ).slice(-2);
		},
		P: function(){
			let timezoneOffset = this.getTimezoneOffset();
			return ( -timezoneOffset < 0 ? '-' : '+' ) + ( '0' + Math.floor( Math.abs( timezoneOffset / 60 ) ) ).slice(-2) + ':' + ( '0' + Math.abs( timezoneOffset % 60 ) ).slice(-2);
		},
		T: function( locale ){
			let timeString = this.toLocaleTimeString( locale, { timeZoneName: 'short' } ).split(' ');
			let abbr = timeString[ timeString.length - 1 ];
			return abbr == 'GMT+1' ? 'CET' : ( abbr == 'GMT+2' ? 'CEST' : abbr );
		},
		Z: function(){ return -this.getTimezoneOffset() * 60 },
		// Full Date/Time
		c: function(){ return this.format('Y-m-d\\TH:i:sP') },
		r: function(){ return this.format('D, d M Y H:i:s O') },
		U: function(){ return Math.floor( this.getTime() / 1000 ) }
	}
	Date.prototype.format = function( formatStr, locale = navigator.language ){
		var date = this;
		return formatStr.replace( /(\\?)(.)/g, function( _, esc, chr ){
			return esc === '' && replaceChars[ chr ] ? replaceChars[ chr ].call( date, locale ) : chr
		})
	}
}).call( this );
function drawColumnOther(data) {
    console.log('drawColumnOther()');
    var $table = $('#table-main');
    var $tbody = $table.find('tbody');
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
                    $this.find("td.size").text(humanFileSize(info.size))
                    var $a = $this.find("td > a");
                    var biclass = getClassByType(info.type)
                    if (biclass != '') {
                        $a.before('<i class="'+biclass+'"></i> ');
                    }
                    else {
                        $a.before('<i class="bi bi-filetype-'+info.type+'"></i> ');
                    }
                    $this.find("td.type").text(info.type.toUpperCase()+' File')
                    // Array.prototype.includes() not support for old browser.
                    var extensionReadByPHP = ['php', 'htaccess'];
                    if (extensionReadByPHP.includes(info.type.toLowerCase())) {
                        $a.attr('href', MyFolder.config.base_path+'/___pseudo/target_directory/public?path='+MyFolder.config.path_info+info.name);
                    }
                }
            }
        });
    }
}
url=MyFolder.config.base_path+MyFolder.config.path_info
function refreshDirectory() {
    var ls = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls',
        directory: MyFolder.config.path_info
      }
    });
    var ls_la = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls -la',
        directory: MyFolder.config.path_info
      }
    });
    ls.done(function (data) {
        drawColumnName(data).then(function () {
            ls_la.done(function (data) {
                drawColumnOther(data)
            })
        })
    })
    console.log('mantab');
    console.log(MyFolder);
    var array = MyFolder.config.path_info.split('/').slice(1,-1);
    var $ol = $('ol.breadcrumb').empty();
    var $li = $('<li></li>').addClass('breadcrumb-item');
    var url = MyFolder.config.base_path;
    var directory = '';
    var info = {type: '.', name: '', directory: directory+'/'}
    var $a = $('<a></a>')
        .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
        .attr('href',url+'/')
        .on('click',gotoLink)
        .data('info',info)
        .data('type',info.type)
        .text(info.name).appendTo($li);
    $('<i class="bi bi-house-door"></i>').appendTo($a);
    $li.appendTo($ol);
    for (i in array) {
        url+='/'+array[i]
        directory+='/'+array[i]
        var $li = $('<li></li>').addClass('breadcrumb-item');
        var info = {type: '.', name: array[i], directory: directory+'/'}
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .attr('href',url+'/')
            .on('click',gotoLink)
            .data('info',info)
            .data('type',info.type)
            .text(info.name).appendTo($li);
        $li.appendTo($ol);
    }
    // $a.before('<i class="bi bi-house-door-fill"></i> ');
    //
    //house-door-fill$a.attr('href', href+'/');
    //
}
$('a.navbar-brand').attr('href',MyFolder.config.base_path);
refreshDirectory()
history.replaceState({path_info: MyFolder.config.path_info}, "", "");
window.onpopstate = (event) => {
    console.log('onpopstate Trigger()');
    var path_info = event.state.path_info;
    MyFolder.config.path_info = path_info
    refreshDirectory()
};

var options= {
    backdrop: 'static',
    keyboard: false
};
const myModal = new bootstrap.Modal(document.getElementById('exampleModal'), options)
const myModalEl = document.getElementById('exampleModal')
console.log(myModal);

if (typeof MyFolder.config.register !== 'undefined') {
    console.log('mantabkoh');
    myModal.show();
    myModalEl.addEventListener('shown.bs.modal', event => {
      // do something...
      console.log(this);
      $('#create-account-next').on('click', function (e) {
          // console.log(e);
          // console.log('abc');
          this.disabled = true
          this.innerText = 'Waiting'
          //$.post().
          let sysadminName = $('#exampleModal').find('[name=sysadminName]')[0].value;
          let sysadminPassword = $('#exampleModal').find('[name=sysadminPassword]')[0].value;
          console.log(sysadminName);
          console.log(sysadminPassword);
          // myModal.hide();
          // $('#exampleModal').find('form').submit();
          senddonk(sysadminName, sysadminPassword);
          // todo, tidka boleh kosong.

      })
    })
}

function senddonk(n,p) {
    url=MyFolder.config.base_path+'/___pseudo/user/create'
    var sed = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'sed',
        name: n,
        pass: p
      }
    });
    sed.done(function (data) {
        console.log(data);
        if (data.success) {
            myModal.hide();
        }
    });
}
