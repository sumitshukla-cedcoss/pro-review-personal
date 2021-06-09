jQuery(document).ready(function($) {
    $(document).on('click', '.mwb_prfw_pin_review', function(){
         var cid = $(this).data('comment-id');
         var pid = $(this).data('post-id');
         $.ajax({
          url: p_custom_param.ajaxurl,
          type: 'POST',
          data: {
               action : 'mwb_prfw_pin_review',
               cid : cid,
               pid : pid,
               nonce    : p_custom_param.nonce
          },
          success: function(data) {
               window.location.reload();
          }
     });
    });
    $(document).on('click', '.mwb_prfw_un_pin_review', function(){
         var cid = $(this).data('comment-id');
         var pid = $(this).data('post-id');
         $.ajax({
          url: p_custom_param.ajaxurl,
          type: 'POST',
          data: {
               action : 'mwb_prfw_unpin_review',
               cid : cid,
               pid : pid,
               nonce    : p_custom_param.nonce
          },
          success: function(data) {
               window.location.reload();
          }
     });
    });
});