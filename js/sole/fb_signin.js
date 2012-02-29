	$('.fb_loading').hide();
	FB.init({appId: fb_app_id, status: true, cookie: true, xfbml: true});
  	FB.Event.subscribe('auth.sessionChange', function(response) {
    	if (response.session) {
      		// A user has logged in, and a new cookie has been saved
			//window.location.reload(true);
    	} else {
      		// The user has logged out, and the cookie has been cleared
    	}
  	});
  	
  	// on successful login of Facebook, retrieve the correct responses and save them
	function fblogin() {
    	FB.login(function(response) {
			
			//console.log(response);
			// get the session object
			//var access_token = response.authResponse.access_token;
			//var secret = response.authResponse.secret;
			//var session_key = response.authResponse.session_key;
			//var sig = response.authResponse.sig;
			//var uid = response.authResponse.uid;
			$('.fb_loading').show();
			
			// let's post the data
			$.ajax({
				url:base + 'auth_other/fb_signin',
				type:'POST',
				data:{user_id:response.authResponse.userID},
				success:function(user_id) {
					window.location = base + 'auth_other/fb_login/' + user_id;
				}
			});
        });
	}  	