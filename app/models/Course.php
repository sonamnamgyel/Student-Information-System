<?php

class Course extends \Eloquent {
	protected $fillable = [];

	protected $table='courses';

	public function staff(){
		return $this->belongsTo('Staff');
	}
	public function programmes(){
		return $this->belongsToMany('Programme')->withPivot('pivot_id','semester_taken','elective','selected','staff_id');
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