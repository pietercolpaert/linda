$('document').ready(function() {

  // Fetch the licenses
    var licenses = [];

    $.getJSON('https://raw.githubusercontent.com/openknowledgebe/be-data-licenses/master/licenses.json', function (data) {
        $.each(data, function (key, val) {
            licenses.push({name : val.title, url : val.url});
        });
    });

    // Use the document on click, we need to catch the event on auto-generated clicks
    $(document).on('click', '.btn-remove-distribution', function(e) {

        e.preventDefault();
        console.log($(this));

        var $form_group = $(this).closest('div.form-group');

        var $input_group = $form_group.nextAll('.distribution:first');

        $form_group.remove();
        $input_group.remove();
    });

    $('.btn-add-distribution').on('click', function(e){
        e.preventDefault();

        var $distribution = $('div#distributions');
        var $form = $('<div class="form-group distribution"></div>');

        // Add the title
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('<label name="distributionTitle" for="input_distributionTitle" class="col-sm-3 control-label">Title</label>');

        $label.appendTo($form);

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);

        var $inputTitle = $('<input name="distributionTitle" id="input_distributionTitle" class="form-control"></select>').append('<option></option>');

        $inputTitle.appendTo($inputContainer);

        // Add the licenses
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('<label for="input_license" class="col-sm-3 control-label">License</label>');

        var html = '<label class="col-sm-2 control-label"></label>\
                    <div class="col-sm-8">\
                        <h4>Distribution</h4>\
                    </div>\
                    <div class="col-sm-2">\
                        <button type="button" class="btn btn-cta btn-remove-distribution"><i class="fa fa-times icon-only"></i></button>\
                    </div>';

        var $header = $('<div class="form-group"></div>')
                        .append(html);

        $header.appendTo($distribution);

        $label.appendTo($form);

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);

        var $selectLicense = $('<select name="license" id="input_license" class="form-control"></select>').append('<option></option>');

        $.each(licenses, function (key, val) {
            $selectLicense.append('<option value="' + val.url + '">' + val.name + "</option>");
        });

        $selectLicense.appendTo($inputContainer);

        // Add the accessURL
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('<label name="accessUrl" for="input_accessUrl" class="col-sm-3 control-label">Access URL</label>');

        $label.appendTo($form);

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);

        var $accessUrl = $('<input name="accessUrl" id="input_accessUrl" class="form-control"></select>').append('<option></option>');

        $accessUrl.appendTo($inputContainer);

        // Add the downloadURL
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('<label name="downloadUrl" for="input_downloadUrl" class="col-sm-3 control-label">Download URL</label>');

        $label.appendTo($form);

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);

        var $downloadURL = $('<input name="downloadUrl" id="input_downloadUrl" class="form-control"></select>').append('<option></option>');

        $downloadURL.appendTo($inputContainer);
    });
});