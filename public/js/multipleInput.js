(function( $ ){

     $.fn.multipleInput = function() {

          return this.each(function() {

               var name = $(this).attr('name');

               // Create html elements
               $list = $('<ul id="' + name + '"/>');

               // If there are already values in there, add them to the custom input control
               var values = $(this).val().split(',');

               $.each(values, function(index) {

                    if (values[index]) {
                         $list.append($('<li class="multipleInput"><span>' + values[index] + '</span></li>')
                              .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                                   .on('click', function(e) {
                                        $(this).parent().remove();
                                        e.preventDefault();
                                   })
                                   )
                              );
                    }
               });

               // Input field
               var $input = $('<input id="' + name + '" class="form-control" type="text" />').keyup(function(event) {

                    if(event.which == 13) {
                         event.preventDefault();
                         // Key press is Enter
                         var val = $(this).val();

                         var $container = event.target.closest('div');

                         $list = $($container.firstChild);

                         $list.append($('<li class="multipleInput"><span>' + val + '</span></li>')
                              .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                                   .on('click', function(e) {
                                        $(this).parent().remove();
                                        e.preventDefault();
                                   })
                              )
                         );

                         $(this).attr('placeholder', '');

                         // empty input
                         $(this).val('');
                    }

               });

               // container div
               var $container = $('<div id="' + name + '" class="multipleInput-container" />').on('click', function() {
                    $input.focus();
               });

               // insert elements into DOM
               $container.append($list).append($input).insertAfter($(this));

               return $(this).hide();
          });

     };


     $.fn.multipleSelect = function() {

          return this.each(function() {

               var name = $(this).attr('name');

               // create html elements
               $list = $('<ul id="' + name + '"/>');

               // If there are already values in there, add them to the custom input control
               var $added = $('#input_' + name + ' option.added');

               // Bind the events to the dropdown select
               var $input = $('<select id="' + name + '" class="form-control" type="text" />').on('change', function (event) {

                    var val = $(this).val();
                    var name = $(this).text();

                    var $container = event.target.closest('div');

                    $list = $($container.firstChild);

                    $list.append($('<li class="multipleInput" value="' + val + '"><span>' + name + '</span></li>')
                         .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                              .click(function(e) {
                                   $(this).parent().remove();
                                   e.preventDefault();
                              })
                              )
                         );

                    // Remove the already selected value from the list of options
                    $($(this).find('option[value="' + val + '"]')).remove();

               });

               $added.each(function() {

                    var optionVal = $(this).attr("value");
                    var optionName = $(this).text();

                    $list.append($('<li class="multipleInput" value="' + optionVal  + '""><span>' + optionName + '</span></li>')
                         .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                              .click(function(e) {
                                   $(this).parent().remove();

                                   // Re-add the option to the list
                                   $input.append('<option value="' + optionVal + '" class="omitted">' + optionName + '</option>');
                                   e.preventDefault();
                              })
                              )
                         );
               });

               $input.append('<option></option>');

               $omitted = $('#input_' + name + ' option.omitted');

               $omitted.each(function() {
                    $input.append($(this));
               });

               // Container div
               var $container = $('<div id="' + name + '" class="multipleInput-container" />').click(function() {
                    $input.focus();
               });

               // Insert elements into DOM
               $container.append($list).append($input).insertAfter($(this));

               return $(this).hide();
          });

     };
})( jQuery );