jQuery(document).ready(function() {

	console.log('ddddd');
	jQuery('#createPostFrom').validate();

	function createPost(fromId){

		jQuery('.outer-section .loader').fadeIn();
		var fileData = jQuery('#featured_img').prop('files')[0];
		var title = jQuery('#title').val();
		var desc = jQuery('#desc').val();
		var excerpt = jQuery('#excerpt').val();
		var post_type = jQuery('#post_type').val();

		

		var formData = jQuery('#createPostFrom').serialize();

		var newForm = new FormData();

		// newForm.append('title', title);
		// newForm.append('desc', desc);
		// newForm.append('excerpt', excerpt);
		// newForm.append('post_type', post_type);
		newForm.append('file', fileData);
		newForm.append('formData', formData);
		newForm.append('action', 'get_data');

		jQuery.ajaxSetup({
            async: true
        });

		$.ajax({
		    url : my_ajax_object.ajax_url,
		    type: 'POST',
		    contentType: false,
		    dataType: 'JSON',
		    cache: false,
            processData: false,
		    data : newForm,
		    success: function(data)
		    {
		    	jQuery('.outer-section .loader').fadeOut();
		        if(data.response == 'faild'){
		        	jQuery('#alert-notification').addClass('faild');
		        	jQuery('#alert-notification').html(data.message);
		        	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
		        }
		        
		        if(data.response == 'success'){
		        	jQuery('#alert-notification').addClass('success');
		        	jQuery('#alert-notification').html(data.message);
		        	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
		        	jQuery('#createPostFrom')[0].reset();
		        }

		        setTimeout(function(){ 
		        	jQuery('#alert-notification').html(''); 
		        }, 8000);
		        
		    },
		    error: function ()
		    {
		 
		    }
		});

		jQuery('#createPostFrom')[0].reset();


	}

	jQuery('#createform_submit_btn').click(function(event){
		event.preventDefault();
		console.log('#ccccc');

		createPost('#createPostFrom');
	});



});