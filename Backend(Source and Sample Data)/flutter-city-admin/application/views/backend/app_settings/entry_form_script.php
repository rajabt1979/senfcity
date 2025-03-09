<script>
	function jqvalidate() {

		$(document).ready(function(){
			$('#app-form').validate({
				rules:{
					title:{
						required: true,
						minlength: 4
					}
				},
				messages:{
					title:{
						required: "<?php echo get_msg('err_title') ?>",
						minlength: "<?php echo get_msg('err_len_title') ?>"
					}
				}
			});
		});

	
	}
</script>