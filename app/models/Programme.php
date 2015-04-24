<?php

class Programme extends \Eloquent {
	protected $fillable = [];
	protected $table='programmes';

	public function department(){
		return $this->belongsTo('Department');
	}

	public function courses(){
		return $this->belongsToMany('Course');
	}
}