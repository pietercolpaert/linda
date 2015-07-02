$('document').ready(function(){

    // Adjust the multivalue fields
    $('.multiInput').multipleInput();
    $('.multiSelect').multipleSelect();

    // Make sure the delete is not a slip of the mouse
    $('.delete').on('click', function(e){

        e.preventDefault();

        var baseURL = document.location.origin;

        // Get the id
        var path = $(this).attr('href').split('/');
        var id = path[path.length-1];

        if(window.confirm('Are you sure you want to delete this?')) {
            $.ajax({
                url: baseURL + '/datasets/' + id,
                method: 'DELETE',
                headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/datasets';
            },
            error: function(e){
                window.location = baseURL + '/datasets';
            }

            });
        } else {
            e.preventDefault();

            window.location = baseURL + '/datasets';
        }
    });

    $('.btn-add-dataset').on('click', function(e){

        e.preventDefault();

        // Get form variables (of active tab)
        //var tab_pane = $('.tab-pane.active');
        var form = $('form.add-dataset');//, tab_pane);

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

        var baseURL = document.location.origin;

        // Ajax call
        $.ajax({
            url: baseURL + '/datasets',
            data: JSON.stringify(data),
            method: 'POST',
            headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/datasets';
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
                    window.location = baseURL + '/datasets/create';
                }
            }
        })
    });

    // Duplicated code for the most part, clean up later
    $('.btn-edit-dataset').on('click', function(e){

        e.preventDefault();

        // Get the id
        var path = window.location.pathname.split('/');
        var id = path[2];


        // Get form variables (of active tab)
        //var tab_pane = $('.tab-pane.active');
        var form = $('form.edit-dataset');//, tab_pane);

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

        var baseURL = document.location.origin;

        // Ajax call
        $.ajax({
            url: baseURL + '/datasets/' + id,
            data: JSON.stringify(data),
            method: 'PUT',
            headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/datasets';
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
                    window.location = baseURL + '/datasets/' + id + '/edit';
                }
            }
        })
    });

});