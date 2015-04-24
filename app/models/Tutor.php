<?php

class Tutor extends \Eloquent {
	protected $fillable = [];

	protected $table='tutors';

	public function department(){
		return $this->belongsTo('Department');
	}

	public function courses(){
		return $this->hasMany('Course');
	}
}