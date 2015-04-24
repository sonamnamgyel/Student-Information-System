@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
</ul>
<!-- ./ tabs -->

{{-- Create Department Form --}}
{{ Form::model($department, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- department code -->
		<div class="form-group {{  $errors->has('department_code') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="department_code"> Department Code </label>
			<div class="col-md-7">
				{{ Form::input('text', 'department_code', null, ['id'=>'department_code','class'=>'form-control']) }}
				{{ $errors->first('department_code', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ department code -->

		<!-- department name -->
		<div class="form-group {{  $errors->has('department_name') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="department_name">Department Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'department_name', null, ['id'=>'department_name','class'=>'form-control']) }}
				{{ $errors->first('department_name', '<span class="help-block">:message</span>') }}
			</div>
		</div>

		<!-- head of department -->
		<div class="form-group {{  $errors->has('hod_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="hod_id">Head of Department</label>
			<div class="col-md-7">
				{{ Form::input('text', 'hod_id', null, ['id'=>'hod_id','class'=>'form-control']) }}
				{{ $errors->first('hod_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ head of department -->

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
