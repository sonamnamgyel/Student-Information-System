<?php

class Department extends \Eloquent {
	//protected $fillable = [];

	protected $table='departments';

	public function programmes(){
		return $this->hasMany('Programme');
	}

	public function courses(){
		return $this->hasManyThrough('Course', 'Programme');
	}

	public function students(){
		return $this->hasManyThrough('Student', 'Programme');
	}
}