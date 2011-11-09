<style type='text/css'>
.radioItem {display: block;}
.radioItem input {position: relative; top: 2px; margin-left: 2px;}
.radioItem span {padding-left: 3px;}
</style>

<div style='line-height: 28px; height: 28px; color: #ffffff; font-weight: bold; background: url(/Themes/BevoMedia/img/ddi2.jpg);'>
	<img align='left' src='/Themes/BevoMedia/img/ddl2.jpg'/>
	Purchase API Credit
	<img style='float:right;' src='/Themes/BevoMedia/img/ddr2.jpg'/>
</div>

<div style='border: solid 1px #2aabe2; padding: 5px;'>

<img align='left' src='/Themes/BevoMedia/img/paypal_logo.png'/>

	<div style='margin-left: 130px; padding: 5px; padding-top: 0px;'>
		Please make sure to follow the entire PayPal process. Once payment is completed and your order is processed, 
		you will be redirected back to a BevoMedia page confirming that credit has been added to your account.
	</div>
	
<br/><br/>
<form id='radioForm'>
<div class='floatLeft' style='margin-right: 50px;'>
	
	<label class='radioItem'>
		<input name='radioAmnt' value='20' checked="checked" type='radio' />
		<span>
			$20
		</span>
	</label>
	
	<br/>
	
	<label class='radioItem'>
		<input name='radioAmnt' value='30' checked="checked" type='radio' />
		<span>
			$30
		</span>
	</label>
	
	<br/>
	
	<label class='radioItem'>
		<input name='radioAmnt' value='50' type='radio' />
		<span>
			$50
		</span>
	</label>
	
	<br/>
</div>

<div class='floatLeft'>
	<label class='radioItem'>
		<input name='radioAmnt' value='100' type='radio' />
		<span>
			$100
		</span>
	</label>
	
	<br/>
	
	<label class='radioItem'>
		<input name='radioAmnt' value='200' checked="checked" type='radio' />
		<span>
			$200
		</span>
	</label>
	
	<br/>
	
	<label class='radioItem'>
		<input name='radioAmnt' value='custom' checked="checked" type='radio' />
		<span>
			Other $<input id="user_amount_id" type="text" value="20.00"/>
		</span>
	</label>
	
	<br/>
	<br/>
</div>
</form>

<a style="position: relative; top: 40px;" class="buttonPay" href="javascript:doForm();"><img style='border:none;' src='/Themes/BevoMedia/img/btn-purchase.png'/></a>

<br class='clearBoth'/>

*All sales are final.

<form action='https://www.paypal.com/cgi-bin/webscr' method='post' name='paypal'>
	<input type='hidden' name='cmd' value='_xclick' />
	<input type='hidden' name='business' value='payments@bevomedia.com' />
	<input type='hidden' name='item_name' value='ADWORDS_API_CREDIT' />
	<input type='hidden' name='currency_code' value='USD' />
	<input type='hidden' name='item_number' value='ADWORDS_API_CREDIT' />

	<input type='hidden' id='amount_id' name='amount' value='20' />
	<input type='hidden' name='tax' value='0' />
	<input type='hidden' name='invoice' value='<?php echo md5(time())?>' />
	<input type='hidden' name='rm' value='2' />
	<input type='hidden' id='return_id' name='return' value='http://<?php echo $_SERVER['HTTP_HOST']?>/BevoMedia/User/AdwordsAPIPaypalReturn.html?PAID=true&amp;AMOUNT=20' />
</form>

</div>


<script language='javascript'>
function doForm()
{
	var rForm = document.getElementById('radioForm').elements['radioAmnt'];
	var rFormVal = false;
	for(var a in rForm)
	{
		if(rForm[a].checked == true)
			rFormVal = rForm[a].value;
	}
	if(rFormVal != 'custom')
	{
		document.getElementById('user_amount_id').value = rFormVal + '.00';
	}

	if(parseInt(document.getElementById('user_amount_id').value,10) < 20)
	{
		alert('The minimum amount allowed is $20.00, thank you.');
		document.getElementById('user_amount_id').value = '20.00';
	}else{
		document.getElementById('return_id').value = 'http://<?php echo $_SERVER['HTTP_HOST']?>/BevoMedia/User/AdwordsAPIPaypalReturn.html?PAID=true&AMOUNT=' + document.getElementById('user_amount_id').value;
		document.getElementById('amount_id').value = document.getElementById('user_amount_id').value;
		if(document.paypal[0].tagName == "FORM")
			document.paypal[0].submit();
		else
			document.paypal.submit();
			
	}
}
</script>
