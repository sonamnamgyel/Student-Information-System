@extends('admin.layouts.modal')
{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
</ul>
<!-- ./ tabs -->

{{-- Create Staff Form --}}
{{ Form::model($staff, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- staff id -->
		<div class="form-group {{  $errors->has('staff_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="staff_id"> Staff ID </label>
			<div class="col-md-7">
				{{ Form::input('text', 'staff_id', null, ['id'=>'staff_id','class'=>'form-control']) }}
				{{ $errors->first('staff_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ staff id -->

		<!-- title -->
		<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="title">Title</label>
			<div class="col-md-7">
				{{ Form::select('title', array(null=>' ','Dr'=>'Dr','Mr'=>'Mr','Ms'=>'Ms'),null, array('class'=>'form-control select2')) }}
				{{ $errors->first('title', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ title -->

		<!-- name -->
		<div class="form-group {{  $errors->has('name') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="name">Full Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'name', null, ['id'=>'name','class'=>'form-control']) }}
				{{ $errors->first('name', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ name -->
		
		<!-- position -->
		<div class="form-group {{  $errors->has('position') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="position">Position</label>
			<div class="col-md-7">
				{{ Form::input('text', 'position', null, ['id'=>'position','class'=>'form-control']) }}
				{{ $errors->first('position', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ position -->
		
		<!-- gender -->
		<div class="form-group {{  $errors->has('gender') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="gender">Gender</label>
			<div class="col-md-7">
				{{ Form::radio('gender', 'Male', (Input::old('gender')== 'Male')) }} Male <br>
				{{ Form::radio('gender', 'Female', (Input::old('gender')== 'Female')) }} Female
				{{ $errors->first('gender', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ gender -->

		<!-- cidno -->
		<div class="form-group {{  $errors->has('cidno') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="cidno">CID Number</label>
			<div class="col-md-7">
				{{ Form::input('text', 'cidno', null, ['id'=>'cidno','class'=>'form-control']) }}
				{{ $errors->first('cidno', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ cidno -->

		<!-- department -->
		<div class="form-group {{  $errors->has('department_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="department_id">Department</label>
			<div class="col-md-7">
			@if($mode=='add' && $dep!=null)	
				{{ Form::select('department_id', array(null=>'-- Select Department --') + $department_lists , $dep , array( 'class' => 'form-control select2')) }}
			@else	
				{{ Form::select('department_id', array(null=>'-- Select Department --') + $department_lists , null , array( 'class' => 'form-control select2')) }}
			@endif	
				{{ $errors->first('department_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ department -->

		<!-- course -->
		<div class="form-group {{  $errors->has('course_id[]') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="course_id[]">Course</label>
			<div class="col-md-7">
			@if($mode=='add')
				{{ Form::selectOptMulti($courses, 'pivot_id[]', 'programme_name', 'module_name','semester_taken','pivot_id', null, array('class'=>'form-control select2')) }}
			@elseif($mode=='edit')
				{{ Form::selectOptMulti($courses, 'pivot_id[]', 'programme_name', 'module_name','semester_taken','pivot_id', $staff_courses, array('class'=>'form-control select2')) }}
			@endif
				{{ $errors->first('course_id', '<span class="help-block">:message</span>') }}                 
			</div>
		</div>

		<!-- ./ course -->

		<!-- phone -->
		<div class="form-group {{  $errors->has('phone') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="phone">Phone Number</label>
			<div class="col-md-7">
				{{ Form::input('text', 'phone', null, ['id'=>'phone','class'=>'form-control', 'placeholder'=>'Optional']) }}
				{{ $errors->first('phone', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ phone -->

		<!-- fax -->
		<div class="form-group {{  $errors->has('fax') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="fax">Fax Number</label>
			<div class="col-md-7">
				{{ Form::input('text', 'fax', null, ['id'=>'fax','class'=>'form-control', 'placeholder'=>'Optional']) }}
				{{ $errors->first('fax', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ fax -->
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
