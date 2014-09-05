(function($){
/*********************************
    Custom jQuery pseudo classes.
*/
$.extend($.expr[':'], {

    "content": function (elem) {
        return $(elem).attr('data-role') === 'content';
    },

    "header": function (elem) {
        return $(elem).attr('data-role') === 'header';
    },

    "footer": function (elem) {
        return $(elem).attr('data-role') === 'footer';
    },

    "controlgroup": function (elem) {
        return $(elem).attr('data-role') === 'controlgroup';
    },

    "navbar": function (elem) {
        return $(elem).attr('data-role') === 'navbar';
    },

    "template": function (elem, i, match) {
        var $elem = $(elem);
        var test1 = elem.nodeName === 'SCRIPT' && $elem.attr('type') === 'text/html';
        if (test1) {
            var param = match[3];
            if (param === undefined) {
                return true;
            }
            else if ($elem.attr('data-template') === param) {
                return true;
            }
        }
        return false;
    },

    "action": function (elem, i, match) {
        var $elem = $(elem);
        var hasAttr = elem.hasAttribute('data-action');
        if (hasAttr) {
            var param = match[3];
            if (param === undefined) {
                return true;
            }
            else if ($elem.attr('data-action') === param) {
                return true;
            }
        }
        return false;
    },
});
}(jQuery))
