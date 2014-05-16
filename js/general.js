function za_create_zip(postID){
	jQuery('.za_download_button').attr('disabled', '').addClass('za_download_button_loading');
	jQuery.ajax({
			type: 'post',
			async: true,
			url : za_ajax.ajax_url,
			data : {action: 'za_create_zip_file', postId: postID},
			success: function(data){
			jQuery('.za_download_button').removeAttr('disabled').removeClass('za_download_button_loading');
              if(data != 'false'){
                window.location = data;
              }              
            }								
	});
}