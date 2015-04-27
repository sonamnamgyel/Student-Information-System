<?php

class Student extends \Eloquent {
	//protected $fillable = ['regno', 'fname'];

	//protected $table='students';
	//protected $primaryKeys = 'stdno';
	
	public function programme(){
		return $this->belongsTo('Programme');
	}

	public function courseprogrammes(){
		return $this->belongsToMany('CourseProgramme', 'course_student');
	}

    public function coursestudents(){
        return $this->hasMany('CourseStudent');
    }

    public function remarks(){
        return $this->hasMany('Remark');
    }

	public function validCourse($course_programme_id,$programme_id){
       return CourseProgramme::where('pivot_id',$course_programme_id)
       				->where('programme_id', $programme_id)
       				->where('course_programme.selected', 'Yes')->first();

	}
	
    public function semesterRoman($number){
        return DB::table('semesters')->where('number', $number)->pluck('roman');
    }

    public function dzongkhagName($id){
        return DB::table('dzongkhags')->where('id', $id)->pluck('name');
    }
    
    public function schoolName($id){
        return DB::table('schools')->where('id', $id)->pluck('name');
    }

    public function getCoursesBySemester($semester, $student){
    	return DB::table('course_student')
    			->leftJoin('course_programme', 'course_student.course_programme_id', '=', 'course_programme.pivot_id')
    			->leftJoin('courses', 'course_programme.course_id', '=', 'courses.id')
                ->select('courses.module_code', 'courses.module_name', 'course_student.type', 'course_programme.programme_id')
    			->where('course_student.student_id', $student->id)
    			//->where('course_programme.programme_id', $student->programme_id)
    			->where('course_programme.semester_taken', $semester)
                ->orderBy('courses.module_code')
    			->get();
    }

    public function getStaffName($id){
        $info= Staff::select('title','name')->where('id', $id)->first();
        return $info->title.". ".$info->name;
    }

}