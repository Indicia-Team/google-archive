/**
 *  Object responsible for form input management.
 */

app = app || {};
app.form = app.form || {};

app.form.inputs = (function(m, $){
    //name under which the record is stored
    m.RECORD =  'record';

    m.KEYS = {
        'SREF' : 'sample:entered_sref',
        'TAXON' : 'occurrence:taxa_taxon_list_id',
        'DATE' : 'sample:date'
    };

    /**
     * Returns the current record.
     * @returns {*}
     */
    m.getRecord = function(){
        return app.storage.tmpGet(m.RECORD);
    };

    /**
     * Sets the current record.
     * @param record The currenr record to be stored.
     */
    m.setRecord = function(record){
        app.storage.tmpSet(m.RECORD, record);
    };

    /**
     * Clears the current record.
     */
    m.clearRecord = function(){
        app.storage.tmpRemove(m.RECORD);
    };

    /**
     * Sets an input in the current record.
     *
     * @param item Input name
     * @param data Input value
     */
    m.set =  function(item, data){
        var record = m.getRecord();
        record[item] = data;
        m.setRecord(record);
    };

    /**
     * Reurns an input value from the current record.
     * @param item The Input name
     * @returns {*}
     */
    m.get = function(item){
        var record = m.getRecord();
        return record[item];
    };

    /**
     * Removes an input from the current record.
     * @param item Input name
     */
    m.remove = function(item){
        var record = m.getRecord();
        delete record[item];
        m.setRecord(record);
    };

    /**
     * Checks if the input is setup
     * @param item Input name
     * @returns {boolean}
     */
    m.is = function(item){
        return !$.isEmptyObject(this.get(item));
    };

    return m;
}(app.form.inputs || {}, app.$ || jQuery));