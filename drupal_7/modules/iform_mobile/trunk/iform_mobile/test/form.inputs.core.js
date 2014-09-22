describe("Core suite", function() {
    beforeEach(function() {
        app.form.inputs.clearRecord();
    });
    afterEach(function() { });

    /**
     * Clearing the record.
     */
    it('clear record', function(){
        //set up input that will not be removed
        var input = 'input';
        var input_data = Math.random();
        app.storage.tmpSet(input, input_data);

        //add record
        var record = {'recordinput': Math.random()};
        app.form.inputs.setRecord(record);

        //remove record
        app.form.inputs.clearRecord();

        //check if the record is removed
        var finalRecord = app.storage.tmpGet(app.form.inputs.RECORD);
        expect(finalRecord).to.be.null;

        //check if the input still exists
        app.storage.tmpGet(input).should.equal(input_data);
    });

    /**
     * Setting up and removing a record.
     */
    it('set record', function() {
        var record = {};
        app.form.inputs.setRecord(record);
        var finalRecord = app.form.inputs.getRecord();
        expect(finalRecord)
            .to.be.an('object')
            .that.is.empty;
    });

    /**
     * Setting up, changing and removing an input.
     */
    it('set input', function() {
        var randNum = Math.random();

        //general setting up a record with an input
        var record = {'input': randNum};
        app.form.inputs.setRecord(record);
        var finalRecord = app.form.inputs.getRecord();
        expect(finalRecord)
            .to.be.an('object')
            .that.has.property('input')
                .that.to.be.equal(randNum);

        //set another input
        var input = 'input2';
        var input_data = Math.random();
        app.form.inputs.set(input, input_data);
        app.form.inputs.get(input).should.equal(input_data);

        //changing input
        input_data = Math.random();
        app.form.inputs.set(input, input_data);
        app.form.inputs.get(input).should.equal(input_data);

        //removing input
        app.form.inputs.remove(input);
        finalRecord = app.form.inputs.getRecord();
        expect(finalRecord).to.not.have.property(input);

    });


});
