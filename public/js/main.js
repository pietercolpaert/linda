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
            url: baseURL + '/dataset',
            data: JSON.stringify(data),
            method: 'POST',
            headers: {
                'Accept' : 'application/json',
                'Content-Type': 'application/json'
                //'Authorization': authHeader
            },
            success: function(e){
                // Done, redirect to datets page
                window.location = baseURL + '/dataset';
            },
            error: function(e){
                if(e.status != 405){
                    var error = JSON.parse(e.responseText);
                    if(error.error && error.error.message){
                        $('.error .text', tab_pane).html(error.error.message)
                        $('.error', tab_pane).removeClass('hide').show().focus();
                    }
                } else{
                    // Ajax followed location header -> ignore
                    window.location = baseURL + '/dataset';
                }
            }
        })
    });

});