<?php

class Account extends \Eloquent {
	protected $fillable = ['student_id'];
	protected $table='fees';
	// protected $primaryKey = 'id';

	public function student(){
		return $this->belongsTo('Student');
	}
}