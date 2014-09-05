(function($){
/*********************************
 Handlebars jQuery function (with compiled template cache).
 */
$.fn.handlebars = function (context) {



    return Handlebars.compile(context);
};

/*
    Wrapper to combine placeholder preparation, template rendering with jQm enhancement.
*/
$.fn.render = function (template, view) {

    // Ensure the placeholder is empty.
    var $this = $(this).empty();



    // Render template into the placeholder and apply jQm enhancements.
    $this.html($(template).handlebars(view)).trigger('create');

    return $this;
};



/*********************************
 Handlebars helpers.
 */

Handlebars.registerHelper('join', function (array, options) {

    var delim = options.hash.delim ? options.hash.delim : ', ';

    var out = '';
    if (Array.isArray(array)) {
        out = array.join(delim);
    }
    return new Handlebars.SafeString(out);
});

// Handlebars.registerHelper('empty', function (it, options) {
//
//     if (Array.isArray(it)) {
//         return !! it.length;
//     }
//     return !! it;
// });

Handlebars.registerHelper('comp', function (v1, operator, v2, options) {

    switch (operator) {
        case '==':
            return (v1 == v2) ? options.fn(this) : options.inverse(this);
        case '===':
            return (v1 === v2) ? options.fn(this) : options.inverse(this);
        case '!==':
            return (v1 !== v2) ? options.fn(this) : options.inverse(this);
        case '<':
            return (v1 < v2) ? options.fn(this) : options.inverse(this);
        case '<=':
            return (v1 <= v2) ? options.fn(this) : options.inverse(this);
        case '>':
            return (v1 > v2) ? options.fn(this) : options.inverse(this);
        case '>=':
            return (v1 >= v2) ? options.fn(this) : options.inverse(this);
        default:
            return options.inverse(this);
    }
});

Handlebars.registerHelper('urlencode', function (it, options) {

    if ($.isPlainObject(it)) {
        return 'TODO';
    }
    return new Handlebars.SafeString(encodeURIComponent(it));
});

// Handlebars.registerHelper('lower', function (it, options) {
//
//     if ($.isPlainObject(it)) {
//         return 'TODO';
//     }
//     return new Handlebars.SafeString(encodeURIComponent(it));
// });

Handlebars.registerHelper('foreach', function (arr, options) {
    if (options.inverse && !arr.length) {
        return options.inverse(this);
    }

    return arr.map(function (item, index) {
        item.$index = index;
        item.$first = index === 0;
        item.$last = index === arr.length - 1;
        item.$odd = arr.length%2 !== 0;
        return options.fn(item);
    }).join('');
});
}(jQuery))