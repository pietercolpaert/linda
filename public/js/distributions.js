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

        var $label = $('<label for="input_license" class="col-sm-3 control-label">License</label>');

        var html = '<label class="col-sm-2 control-label"></label>\
                    <div class="col-sm-8">\
                        <h4>Distribution</h4>\
                    </div>\
                    <div class="col-sm-2">\
                        <button class="btn btn-cta btn-remove-distribution"><i class="fa fa-times icon-only"></i></button>\
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


        // Add the multiple input usecases
        var $inputContainer = $('<div class="col-sm-9"></div>');

        var $label = $('<label name="usecase" for="input_usecase" class="col-sm-3 control-label">Usecases</label>');

        $label.appendTo($form);

        $form.appendTo($distribution);

        $inputContainer.appendTo($form);

        var $selectUsecase = $('<select name="usecase" id="input_usecase" class="form-control"></select>').append('<option></option>');

        $.each(usecases, function (key, val) {
            $selectUsecase.append('<option value="' + val.url + '" class="omitted">' + val.name + "</option>");
        });

        $selectUsecase.appendTo($inputContainer);
        $selectUsecase.multipleSelect();

    });
});