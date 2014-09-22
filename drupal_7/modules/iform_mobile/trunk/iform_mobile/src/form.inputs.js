/**
 *  Object responsible for form input management.
 */

app = app || {};
app.form = app.form || {};

app.form.inputs = (function(m, $){
    m.KEYS = {
        'SREF' : 'sample:entered_sref',
        'TAXON' : 'occurrence:taxa_taxon_list_id',
        'DATE' : 'sample:date'
    };

    m.set =  function(item, data){
        app.storage.tmpSet(item, data);
    };

    m.get = function(item){
        app.storage.tmpGet(item);
    };

    m.remove = function(item){
        app.storage.tmpRemove(item);
    };

    m.is = function(item){
        return !$.isEmptyObject(this.get(item));
    };

    return m;
}(app.form.inputs || {}, app.$ || jQuery));