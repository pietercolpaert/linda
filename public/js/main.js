$('document').ready(function(){

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

        console.log(data);
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

        console.log(data);
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