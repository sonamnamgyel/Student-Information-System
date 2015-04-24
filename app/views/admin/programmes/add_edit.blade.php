@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
</ul>
<!-- ./ tabs -->

{{-- Add Programme Form --}}
{{ Form::model($programme, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- programme code -->
		<div class="form-group {{  $errors->has('programme_code') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="programme_code"> Programme Code </label>
			<div class="col-md-7">
				{{ Form::input('text', 'programme_code', null, ['id'=>'programme_code','class'=>'form-control', 'placeholder'=>'BE (IT)']) }}
				{{ $errors->first('programme_code', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ programme code -->

		<!-- programme name -->
		<div class="form-group {{  $errors->has('programme_name') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="programme_name">Programme Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'programme_name', null, ['id'=>'programme_name','class'=>'form-control', 'placeholder'=>'Bachelor of Information Technology Engineering']) }}
				{{ $errors->first('programme_name', '<span class="help-block">:message</span>') }}
			</div>
		</div>

		<!-- department -->
		<div class="form-group {{  $errors->has('department_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="department_id">Department</label>
			<div class="col-md-7">
				{{ Form::select('department_id', array(null=>'-- Select Department --') + $department_lists , null , array( 'class' => 'form-control select2')) }}
				{{ $errors->first('department_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ department -->

	</div>
	<!-- ./ general tab -->

</div>
<!-- ./ tabs content -->

<!-- Form Actions -->
<div class="form-group">
	<div class="col-md-offset-2 col-md-8">
		<button type="button" class="btn btn-warning close_popup">Cancel</button>
		<button type="reset" class="btn btn-default">Reset</button>
		<button type="submit" class="btn btn-success">Submit</button>
	</div>
</div>
<!-- ./ form actions -->
{{ Form::close() }}
@stop
