
(function($){
    "use strict";
    $(document).ready(function() {
      
	     $('#formsub').submit(function(){
         var count = $('#ppp').val();

          var data = {
                      'action': 'ajaxProsessData',
                      'count' : count
                      };

                            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                      $.post(ajaxurl, data, function(response) {
                             // alert('Got this from the server: ' + response);
                              $('#allpost').html(response);
                         });
           return false;
	     });

    });
})(jQuery);

           