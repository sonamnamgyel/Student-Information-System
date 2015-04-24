<?php

class Configuration extends \Eloquent {

	public function semesterList($status){
		if($status=="Winter")
			return array(null=>'--Select Semester--','1'=>'I','3'=>'III','5'=>'V','7'=>'VII','9'=>'IX');
		elseif ($status == "Summer") {
			return array(null=>'--Select Semester--','2'=>'II','4'=>'IV','6'=>'VI','8'=>'VIII','10'=>'X');
		}
	}

	public function semesterRoman($semester){
    	if($semester=='1') return "I";
    	elseif ($semester=='2') return "II";
    	elseif ($semester=='3') return "III";
        elseif ($semester=='4') return "IV"; 
    	elseif ($semester=='5') return "V"; 
		elseif ($semester=='6') return "VI";
        elseif ($semester=='7') return "VII";
    	elseif ($semester=='8') return "VIII";
		elseif ($semester=='9') return "IX";
	    elseif ($semester=='10') return "X";
        else return "--";
    }
}