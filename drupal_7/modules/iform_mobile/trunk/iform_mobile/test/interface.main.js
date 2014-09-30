/**
 * Interface testing functions.
 *
 * Until the code is fully covered in tests, it is the most important to
 * test the library functions/interface that the mobile apps directly call.
 */

describe('app interface', function(){

    /**
     * Testing:
     app
     app.CONF
     app.TRUE
     app.ERROR

     app.data
     app.settings
     */
    it('main', function(){
        expect(app).to.exist;
        expect(app.CONF).to.exist;
        expect(app.TRUE).to.exist;
        expect(app.ERROR).to.exist;
        expect(app.data).to.exist;
        expect(app.settings).to.exist;
    })
});

describe('record interface', function(){
    beforeEach(function(){
        app.record.clear();
        app.record.storage.clear();
    });
    afterEach(function(){});

    /**
     * Testing:
     app.record.validate()
     app.record.clear()
     */
    it('main', function(){

    });

    /**
     * Testing:
     app.record.storage.remove(savedRecordId)
     app.record.storage.save(onSaveSuccess);
     app.record.storage.getAll()
     */
    it('storage', function(){
        //SAVE
        app.record.storage.save(function(savedRecordId){
            //GETALL
            var records = app.record.storage.getAll();
            expect(records).to.be.an.object;
            var keys = Object.keys(records);
            expect(keys.length).to.be.equal(1);

            expect(savedRecordId).to.be.equal(1);

            //REMOVE
            app.record.storage.remove(savedRecordId);
            var records = app.record.storage.getAll();
            expect(records).to.be.an.object;
            var keys = Object.keys(records);
            expect(keys.length).to.be.equal(0);
        });
    });

    /* Testing:
     app.record.inputs.KEYS.*
     app.record.inputs.set(input, data)
     app.record.inputs.is(input)
     */
    it('inputs', function(){
        //KEYS
        expect(app.record.inputs.KEYS).to.be.array;

        //SET
        var input = 'input';
        var input_data = Math.random();

        app.record.inputs.set(input, input_data);
        var f_input_data = app.record.inputs.get(input);
        expect(f_input_data).to.equal(input_data);

        //IS
        var exist = app.record.inputs.is(input);
        expect(exist).to.be.true;
    });
});

/**
 * Testing:
 app.auth.CONF
 app.auth.removeUser();
 app.auth.setUser(user);
 app.auth.isUser();
 */
describe('authentication interface', function(){
    beforeEach(function(){
        app.auth.removeUser()
    });
    afterEach(function(){});

    it('main', function(){
        //CONF
        expect(app.auth.CONF).to.be.object;

        //SET
        var user = {
            'name': 'Tom',
            'surname': 'Jules',
            'email': 'tom@jules.com',
            'usersecret': Math.random()
        };
        app.auth.setUser(user);
        var f_user = app.auth.getUser();
        expect(f_user).to.be.an.object;
        expect(f_user).to.have.property('name', 'Tom');

        //IS
        var exists = app.auth.isUser();
        expect(exists).to.be.true;

        //REMOVE
        app.auth.removeUser();
        exists = app.auth.isUser();
        expect(exists).to.be.false;

        f_user = app.auth.getUser();
        expect(f_user).not.to.be.null;

        //checking if getting a user hasn't initialised one
        exists = app.auth.isUser();
        expect(exists).to.be.false;

    });
});

describe('navigation interface', function(){
    beforeEach(function(){});
    afterEach(function(){});

    it('main', function(){

    });
});

describe('storage interface', function(){
    beforeEach(function(){
        app.storage.tmpClear();
    });
    afterEach(function(){});

    /**
     * Testing:
     app.storage.tmpGet
     app.storage.tmpSet
     */
    it('main', function(){
        //SET
        var item = 'item';
        var item_data = Math.random();
        app.storage.tmpSet(item, item_data);

        var exists = app.storage.tmpIs(item);
        expect(exists).to.be.true;

        //GET
        var f_item_data = app.storage.tmpGet(item);
        expect(f_item_data).to.exist;
        expect(f_item_data).to.be.equal(item_data);

    });
});

/**
 * ...................
 * To Cover in tests
 * ...................
 *
 app.record.validate(recordId)

 app.geoloc.set
 app.geoloc.get()
 app.geoloc.start()
 app.geoloc.validate()

 app.io.sendSavedForm()
 app.io.sendAllSavedRecords()
 app.io.sendSavedRecord(savedRecordId)

 app.navigation.makePopup()
 app.navigation.popup()
 app.navigation.go()

 ###########
 Events:
 app.submitRecord.start
 app.record.sent.success
 app.record.sent.error
 app.record.invalid
 app.submitRecord.save
 app.submitRecord.end
 app.geoloc.lock.timeout
 app.geoloc.lock.error
 app.geoloc.lock.start
 app.geoloc.lock.no
 app.geoloc.lock.bad
 app.geoloc.lock.ok
 app.geoloc.noGPS
 app.record.sentall.success




 */