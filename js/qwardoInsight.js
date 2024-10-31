jQuery(document).ready(function(){
			function isGuid(value)
			{    
				var regex = /[a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12}/i;
				var match = regex.exec(value);
				return match != null;
			}
			var inputValue= jQuery("#trackingidform input[type='text']").val();
			if(inputValue != undefined && inputValue.trim().length >= 0 && isGuid(inputValue))
				{
					jQuery("#trackingidform input[type='text']").attr("disabled","disabled");
					jQuery("#trackingidform #submit").attr("disabled","disabled");
				}
			jQuery("#trackingidform").on('submit', function(e){
				
				jQuery("#site-id-null").addClass("site-id-null");
				jQuery("#site-id-not-valid").addClass("site-id-not-valid");
				
				var inputValue= jQuery("#trackingidform input[type='text']").val();
				if(inputValue == undefined || inputValue.trim().length == 0)
				{
					jQuery("#site-id-null").removeClass("site-id-null");
					e.preventDefault();
					return;
				}
				else if(!isGuid(inputValue))
				{
					jQuery("#site-id-not-valid").removeClass("site-id-not-valid");
					e.preventDefault();
					return;
				}
				
			});
		});
		