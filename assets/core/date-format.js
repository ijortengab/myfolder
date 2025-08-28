/**
 * Additional method for object Date called `.format`.
 *
 * @ref
 *   https://gist.github.com/kubiqsk/c60207a3075104df7cc1822a95053ecd
 *   php.net/date
 *
 * Meng-extend object Date dengan menambahkan method `.format`.
 * Format disesuaikan dengan style-nya PHP.
 */
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
