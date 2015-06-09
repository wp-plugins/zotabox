jQuery(window).ready(function(){
	jQuery("#button-open-insert-code").click(function(){
		jQuery(".insert-code-content").fadeIn(function(){
			jQuery(".insert-code-content").addClass("active");
		});
		

		if(jQuery(".insert-code-wrapper .active").length == 1){
			jQuery(".insert-code-content").fadeOut(function(){
				jQuery(".insert-code-content").removeClass("active");
			});
		}
	});
	jQuery("#disconnect-ztb").click(function(){
		var r = confirm("Do you want to disconnect from Zotabox?");
		if (r == true) {
			jQuery(".loading").css("display","inline-block");
           jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php?action=disconnect_ztb',
				data: {type: 'disconnect_ztb'},
				success: function(){
					disconnectZTB();
				}
			});
        }
	});

	jQuery(".insert-code").click(function(e){
		e.preventDefault();
		jQuery.ajax({
			type: "POST",
			url: '/wp-admin/admin-ajax.php?action=clear_account',
			data: {type: 'disconnect_ztb'},
			success: function(){
				jQuery("#insert-ztbcode-form").submit();
			}
		});
	});

	jQuery("#connect-ztb").click(function(){
		createToken();
	});
	function checkToken(){
		var interVal = setInterval(function(){
			jQuery.ajax({
				type: "POST",
				url: '/wp-admin/admin-ajax.php?action=check_ztbtoken',
				data: {type:'check token'},
				success: function(response){
					if(response.error == false){
						clearInterval(interVal);
						location.reload();
					}
				}
			});
		}, 3000);
	}

	function createToken(){
		jQuery.ajax({
			type: "POST",
			url: '/wp-admin/admin-ajax.php?action=create_ztbtoken',
			data: {type:'create token'},
			success: function(response){
				checkToken();
			}
		});
	}
	function disconnectZTB(){
		var data = {
			customer: jQuery("#ztb_id").val(),
			redirect: '/wp-admin/admin-ajax.php?action=disconnect_ztb',
			disconnect: true,
			token: ''
		};
		jQuery.ajax({
			type: "POST",
			url: 'http://widgets.zotabox.com/customer/access/disconnect',
			data: data,
			success: function(){
				jQuery(".loading").fadeOut();
				location.reload();
			}
		});
	}

});