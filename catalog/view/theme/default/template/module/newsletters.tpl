<script>
		function subscribe()
		{
			var emailpattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			var email = $('#txtemail').val();
			if(email != "")
			{
				if(!emailpattern.test(email))
				{
					alert("Неправильный Email");
					return false;
				}
				else
				{
					$.ajax({
						url: 'index.php?route=module/newsletters/news',
						type: 'post',
						data: 'email=' + $('#txtemail').val(),
						dataType: 'json',
						
									
						success: function(json) {
						
						alert(json.message);
						
						}
						
					});
					return false;
				}
			}
			else
			{
				alert("Email обязательно");
				$(email).focus();
				return false;
			}
			

		}
	</script>
	
<div class="subscribe-box" style="margin-bottom: 20px;">
	<h3 class="subscribe-title"><?php echo $heading_title; ?></h3>
	<div class="subscribe-form">
		<form class="form-inline" action="" method="post">
		  <div class="form-group required">
		    <label class="sr-only" for="exampleInputAmount">Email:</label>
		    <input type="email" class="form-control" name="txtemail" id="txtemail" placeholder="Email">
		  </div>
		  <button type="submit" class="btn btn-primary" onclick="return subscribe();">Подписаться</button>
		</form>      
	</div>
</div>
