(function( $ ){

     $.fn.multipleInput = function() {

          return this.each(function() {

               var name = $(this).attr('name');

               // create html elements
               $list = $('<ul id="' + name + '"/>');

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
               var $input = $('<input id="' + name + '" class="form-control" type="text" />').keyup(function(event) {

                    if(event.which == 13) {
                         // key press is Enter
                         var val = $(this).val();

                         var $container = event.target.closest('div');

                         $list = $($container.firstChild);

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
               var $container = $('<div id="' + name + '" class="multipleInput-container" />').click(function() {
                    $input.focus();
               });

               // insert elements into DOM
               $container.append($list).append($input).insertAfter($(this));

               return $(this).hide();
          });

     };
})( jQuery );