<?php

class Staff extends \Eloquent {
	protected $fillable = [];

	//protected $table='staffs';

	public function department(){
		return $this->belongsTo('Department');
	}

    public function courseDistinct($staff_id){
        return DB::table('course_programme')
                ->select('course_id')->where('staff_id', $staff_id)
                ->where('selected', 'Yes')
                ->distinct()->get();
    }

    public function courseProgrammeDetails($course_id, $staff_id){
        return DB::table('course_programme')
            ->where('course_id', $course_id)
            ->where('staff_id', $staff_id)->get();
    }

    public function courseDetailsById($id){
    	$course = Course::select('module_code', 'module_name')->where('id',$id)->first();
        return $course->module_code.' - '.$course->module_name;
    }

    public function programmeName($id){
    	return Programme::find($id)->pluck('programme_name');
    }

    public function semesterRoman($number){
        return DB::table('semesters')->where('number', $number)->pluck('roman');
    }

    public function departmentName($id){
        return Department::where('id', $id)->pluck('department_name');
    }
}