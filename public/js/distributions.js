$('document').ready(function() {

  // Fetch the licenses
    var licenses = [];

    $.getJSON('https://raw.githubusercontent.com/openknowledgebe/be-data-licenses/master/licenses.json', function (data) {
        $.each(data, function (key, val) {
            licenses.push({name : val.title, url : val.url});
        });
    });

    // Fetch the use cases
    var usecases = [];

    $.getJSON(window.location.origin + '/lists/usecases', function (data) {
        usecases = data;
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
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('');

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

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);
        var $inputTitle = $('<label for="input_distributiontitle" class="col-sm-9 control-label">Title</label><input type="text" hint="Distribution title" name="distributiontitle" id="input_distributiontitle" class="form-control"/>');

        var $inputDownloadUrl = $('<label for="input_downloadurl" class="col-sm-9 control-label">Direct download url, if exists</label><input type="text" hint="Download url" name="downloadurl" id="input_downloadurl" class="form-control"/>');
        var $inputAccessUrl = $('<label for="input_accessurl" class="col-sm-9 control-label">Link to a page where I can get download instructions</label><input type="text" hint="Access url" name="accessurl" id="input_accessurl" class="form-control"/>');
        $inputTitle.appendTo($inputContainer);
        $inputDownloadUrl.appendTo($inputContainer);
        $inputAccessUrl.appendTo($inputContainer);
        var $licenseLabel = $('<label for="input_license" class="col-sm-9 control-label">License</label>');
        var $selectLicense = $('<select name="license" id="input_license" class="form-control"></select>').append('<option></option>');

        $.each(licenses, function (key, val) {
            $selectLicense.append('<option value="' + val.url + '">' + val.name + "</option>");
        });
        $licenseLabel.appendTo($inputContainer);
        $selectLicense.appendTo($inputContainer);
      
    });
});
