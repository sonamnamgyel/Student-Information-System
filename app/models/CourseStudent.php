<?php

class CourseStudent extends \Eloquent {
	protected $fillable = [];

	protected $table='course_student';

	public function student(){
		return $this->belongsToMany('Student');
	}
}