var registerFormValidation = {
	_form_name: "registerForm",
	
	requiredFields: new Array('FirstName', 'LastName', 'Email', 'Password', 're-enter_password', 'Address', 'City', 'State', 'Zip', 'Country', 'Phone', 'HowHeard'),
	matchFields: new Array( new Array('Password', 're-enter_password') ),
	emailFields: new Array('Email'),
	
	_$: function(eleName)
	{
		if(document.getElementById(eleName))
		{
			return document.getElementById(eleName);
		}
	},
	
	validateForm: function()
	{
		if(arguments.length > 0)
		{
			this.requiredFields = new Array('FirstName', 'LastName', 'Address', 'City', 'State', 'Zip', 'Country', 'Phone', 'HowHeard');
			this.matchFields = this.emailFields = new Array();
		}
		
		var valid = true;
		var form = document.forms[this._form_name];

		for(var ele in this.requiredFields)
		{
			ele = this.requiredFields[ele];
			var element = form[ele];
			this._$(ele.toLowerCase()+'_validation_id').style.display = 'none';
		}
		
		var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		for(var ele in this.emailFields)
		{
			ele = this.emailFields[ele];
			var element = form[ele];
			if(element.value.match(emailRegEx))
			{
			
			}else{
				valid = false;
				this._$(ele.toLowerCase()+'_validation_id').style.display = 'inline';
				this._$(ele.toLowerCase()+'_validation_id').innerHTML = 'The ' + ele + ' field is not a valid email address...';
				
			}
		}
		
		for(var ele in this.matchFields)
		{
			ele = this.matchFields[ele];
			
			var element0 = form[ele[0]];
			var element1 = form[ele[1]];			
			
			if(element0.value == element1.value)
			{

			}else{
				valid = false;
				this._$(ele[1].toLowerCase()+'_validation_id').style.display = 'inline';
				this._$(ele[1].toLowerCase()+'_validation_id').innerHTML = 'The ' + ele[0] + ' value must match the ' + ele[1].replace('_', ' ') + ' value...';
			}
		}
		
		for(var ele in this.requiredFields)
		{
			ele = this.requiredFields[ele];
			var element = form[ele];
			if(element.value == '')
			{
				valid = false;
				this._$(ele.toLowerCase()+'_validation_id').style.display = 'inline';
				this._$(ele.toLowerCase()+'_validation_id').innerHTML = 'The ' + ele.replace('_', ' ') + ' field can\'t be empty...';
					
			}else{
				
			}
		}
		
		if(!valid)
			return false;
	}
}
