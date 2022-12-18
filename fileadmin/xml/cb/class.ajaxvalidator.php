<?php

class ajaxvalidator {
    
	function sendForm() {
		/*
			Method majixValidate without arguments. 
			Display an alert message if there are errors
			Else, submit the form
		*/
		return $this->oForm->majixValidate();
	}
	
	function checkEmail() {
		/*
			Method majixValidate with a callbak function.			
		*/
		return $this->oForm->rdt('email')->majixValidate('cb.displayInfo()');
	}
	
	function displayInfo($aParams) {
		/*
			$aParams is an array which contains error(s)
		*/
		if(empty($aParams)) {
			return $this->oForm->rdt('email_info')->majixSetInvisible();
		}
		
		return array(
			$this->oForm->rdt('email_info')->majixSetHtml($aParams['message']),
			$this->oForm->rdt('email_info')->majixSetVisible(),			
		);
	}

}

?>