<?php print form::open($controllerpath.'/upload_mappings', array('ENCTYPE'=>'multipart/form-data')); ?>
<label for="csv_upload">Upload a CSV file into this list:</label>
<input type="file" name="csv_upload" id="csv_upload" size="40" />
<input type="submit" value="Upload CSV File" />
</form>
