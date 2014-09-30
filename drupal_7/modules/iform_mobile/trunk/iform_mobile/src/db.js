/**
 *  Module responsible for large data management.
 */

app = app || {};
app.db = (function(m, $){
    //m.init = function(){
    //    //window.indexedDB = window.indexedDB || window.webkitIndexedDB || window.mozIndexedDB || window.OIndexedDB || window.msIndexedDB,
    //    //    IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.OIDBTransaction || window.msIDBTransaction,
    //    //    dbVersion = 1;
    //    //
    //    if (typeof window.mozIndexedDB !== "undefined") {
    //        window.indexedDB = window.mozIndexedDB;
    //    }
    //    else {
    //        window.indexedDB = window.shimIndexedDB;
    //        window.shimIndexedDB.__useShim();     // force to use polyfill
    //        window.shimIndexedDB.__debug(true);   // enable debug
    //        console.log("Starting Tests with shimIndexedDB");
    //    }
    //
    //};


    return m;
}(app.db || {}, app.$ || jQuery));