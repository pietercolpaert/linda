$('document').ready(function(){

    // Adjust the multivalue fields
    $('.multiInput').multipleInput();
    $('.multiSelect').multipleSelect();

    // Make sure the delete is not a slip of the mouse
    $('.delete').on('click', function(e){

        e.preventDefault();

        var baseURL = document.location.origin;
        var pieces = document.location.pathname.split('/');
        var domain = pieces[1];

        // Get the id
        var path = $(this).attr('href').split('/');
        var id = path[path.length-1];

        if(window.confirm('Are you sure you want to delete this?')) {
            $.ajax({
                url: baseURL + '/' + domain + '/' + id,
                method: 'DELETE',
                headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/' + domain;
            },
            error: function(e){
                window.location = baseURL + '/' + domain;
            }

            });
        } else {
            e.preventDefault();

            window.location = baseURL + '/' + domain;
        }
    });

    $('.btn-add').on('click', function(e){

        e.preventDefault();

        var pieces = document.location.pathname.split('/');
        var domain = pieces[1];

        // Get form variables (of active tab)
        //var tab_pane = $('.tab-pane.active');
        var form = $('form.add');//, tab_pane);

        // Loop through fields
        var data = new Object();

        $('input, textarea, select', form).each(function(){
            if($(this).attr('name')){
                if($(this).attr('name') == 'collection'){
                    collection = $(this).val();
                }else{
                    // Regular fields
                    if($(this).attr('type') == 'checkbox'){
                        data[$(this).attr('name')] = $(this).attr('checked') ? 1 : 0;
                    }else{
                        data[$(this).attr('name')] = $(this).val();
                    }
                }
            }
        });

        // Gather multi value fields
        $('.multiInput').each(function() {

            var name = $(this).attr('name');
            var values = [];
            $(this).next('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            data[name] = values;
        });

        $('.multiSelect').each(function() {

            var name = $(this).attr('name');
            var values = [];

            $(this).next('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            data[name] = values;
        });

        data['distributions'] = [];

        $('.distribution').each(function() {

            var obj = {'license': '', 'usecases': []};

            var name = $(this).attr('name');

            var $license = $(this).find('select#input_license');

            obj['license'] = $license.find(':selected').val();

            var values = [];

            $(this).find('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            obj['usecases'] = values;

            data['distributions'].push(obj);
        });

        var baseURL = document.location.origin;

        // Ajax call
        $.ajax({
            url: baseURL + '/' + domain,
            data: JSON.stringify(data),
            method: 'POST',
            headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/' + domain;
            },
            error: function(e){
                if(e.status != 405){
                    var error = JSON.parse(e.responseText);
                    if(error.error && error.error.message){
                        $('.error .text').html(error.error.message)
                        $('.error').removeClass('hide').show().focus();
                    }
                    window.scrollTo(0,0);
                } else{
                    // Ajax followed location header -> ignore
                    window.location = baseURL + '/' + domain + '/create';
                }
                window.scrollTo(0,0);
            }
        })
    });

    $('.btn-edit').on('click', function(e){

        e.preventDefault();

        var pieces = document.location.pathname.split('/');
        var domain = pieces[1];

        // Get the id
        var path = window.location.pathname.split('/');
        var id = path[2];


        // Get form variables (of active tab)
        //var tab_pane = $('.tab-pane.active');
        var form = $('form.edit');//, tab_pane);

        // Loop through fields
        var data = new Object();

        $('input, textarea, select', form).each(function(){
            if($(this).attr('name')){
                if($(this).attr('name') == 'collection'){
                    collection = $(this).val();
                }else{
                    // Regular fields
                    if($(this).attr('type') == 'checkbox'){
                        data[$(this).attr('name')] = $(this).attr('checked') ? 1 : 0;
                    }else{
                        data[$(this).attr('name')] = $(this).val();
                    }
                }
            }
        });

        // Gather multi value fields
        $('.multiInput').each(function() {

            var name = $(this).attr('name');
            var values = [];
            $(this).next('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            data[name] = values;
        });

        $('.multiSelect').each(function() {

            var name = $(this).attr('name');
            var values = [];
            $(this).next('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            data[name] = values;
        });

        data['distributions'] = [];

        $('.distribution').each(function() {

            var obj = {'license': '', 'usecases': []};

            var name = $(this).attr('name');

            var $license = $(this).find('select#input_license');

            obj['license'] = $license.find(':selected').val();

            var values = [];

            $(this).find('div.multipleInput-container').first().find('li').each(function(){
                values.push($(this).text());
            });

            obj['usecases'] = values;

            data['distributions'].push(obj);
        });

        var baseURL = document.location.origin;

        // Ajax call
        $.ajax({
            url: baseURL + '/' + domain + '/' + id,
            data: JSON.stringify(data),
            method: 'PUT',
            headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/' + domain;
            },
            error: function(e){
                if(e.status != 405){
                    var error = JSON.parse(e.responseText);
                    if(error.error && error.error.message){
                        $('.error .text').html(error.error.message)
                        $('.error').removeClass('hide').show().focus();
                    }
                } else{
                    // Ajax followed location header -> ignore
                    window.location = baseURL + '/' + domain + '/' + id + '/edit';
                }
                window.scrollTo(0,0);
            }
        })
    });
});