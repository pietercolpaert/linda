(function( $ ){

     $.fn.multipleInput = function() {

          return this.each(function() {

               // create html elements
               $list = $('<ul/>');

               // If there are already values in there, add them to the custom input control
               var values = $(this).val().split(',');

               $.each(values, function(index) {

                    if (values[index]) {
                         $list.append($('<li class="multipleInput"><span>' + values[index] + '</span></li>')
                              .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                                   .click(function(e) {
                                        $(this).parent().remove();
                                        e.preventDefault();
                                   })
                                   )
                              );
                    }
               });

               // input
               var $input = $('<input class="form-control" type="text" />').keyup(function(event) {

                    if(event.which == 188) {
                         // key press is space or comma
                         var val = $(this).val().slice(0, -1); // remove space/comma from value

                         $list.append($('<li class="multipleInput"><span>' + val + '</span></li>')
                              .append($('<a href="#" class="fa fa-times icon-only" title="Remove" />')
                                   .click(function(e) {
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
               var $container = $('<div class="multipleInput-container" />').click(function() {
                    $input.focus();
               });

               // insert elements into DOM
               $container.append($list).append($input).insertAfter($(this));

               var $orig = $(this);

               return $(this).hide();

          });

     };
})( jQuery );