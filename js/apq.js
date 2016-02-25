
(function($){
    "use strict";
    $(document).ready(function() {
      
	     $('#formsub').submit(function(){
         var count = $('#ppp').val();

           $.post(
                    ajaxurl, 
                       {
                         'action': 'ajaxProsessData',
                         'count' : count
                       }, 
                       function(response){
                            //alert('The server responded: ' + response);
                            $('#allpost').html(response);
                       }
                       );
           return false;
	     });

    });
})(jQuery);