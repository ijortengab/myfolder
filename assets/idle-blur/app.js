MyFolder = window.MyFolder || {}

MyFolder.idleBlur = function () {
    this.timeout = undefined;
    // Place instance to variable.
    let that = this;
    // Body event.
    $('body').on('mouseover keydown',function () {
        // Check
        if (that.timeout) {
            clearTimeout(that.timeout);
        }
        that.waiting()
    });
    $('body').on('dblclick',function () {
        $(this).toggleClass('idle-blur');
    });
    this.waiting = function () {
        this.timeout = setTimeout(function () {
            $('body').addClass('idle-blur');
            that.waiting();
        }, 30000);
    }
    this.waiting()
}

$(document).ready(function () {
    MyFolder.idleBlur.instance = new MyFolder.idleBlur;
})
