@extends('admin.layouts.modal')
<script src="{{ url('adminlte/js/jquery.min.js') }}"></script>
{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
	@if($mode=='edit')
		<li><a href="#tab-remark" data-toggle="tab">Remarks</a></li>
	@endif
</ul>
<!-- ./ tabs -->

{{-- Create Student Form --}}
{{ Form::model($student, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- student number -->
		<div class="form-group {{ $errors->has('stdno') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="stdno"> Student Number </label>
			<div class="col-md-7">
				{{ Form::input('text', 'stdno', null, ['id'=>'stdno','class'=>'form-control']) }}
				{{ $errors->first('stdno', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ student number -->

		<!-- firstname -->
		<div class="form-group {{ $errors->has('fname') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="fname">First Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'fname', null, ['id'=>'fname','class'=>'form-control']) }}
				{{ $errors->first('fname', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ firstname -->

		<!-- middlename -->
		<div class="form-group {{ $errors->has('mname') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="mname">Middle Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'mname', null, ['id'=>'mname','class'=>'form-control', 'placeholder'=>'Optional']) }}
				{{ $errors->first('mname', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ middlename -->
		
		<!-- lastname -->
		<div class="form-group {{ $errors->has('lname') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="lname">Last Name</label>
			<div class="col-md-7">
				{{ Form::input('text', 'lname', null, ['id'=>'lname','class'=>'form-control']) }}
				{{ $errors->first('lname', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ lastname -->

		<!-- cidno -->
		<div class="form-group {{ $errors->has('cidno') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="cidno">CID No</label>
			<div class="col-md-7">
				{{ Form::input('text', 'cidno', null, ['id'=>'cidno','class'=>'form-control']) }}
				{{ $errors->first('cidno', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ cidno -->
		
		<!-- sex -->
		<div class="form-group {{ $errors->has('sex') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="sex">Sex</label>
			<div class="col-md-7">
				{{ Form::radio('sex', 'Male', (Input::old('sex')== 'Male')) }} Male <br>
				{{ Form::radio('sex', 'Female', (Input::old('sex')== 'Female')) }} Female
				{{ $errors->first('sex', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ sex -->

		<!-- dob -->
		<div class="form-group {{ $errors->has('dob') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="dob">Date of Birth</label>
			<div class="col-md-7">
				{{ Form::input('date', 'dob', null, ['id'=>'dob','class'=>'form-control']) }}
				{{ $errors->first('dob', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ dob -->

		<!-- student-type -->
		<div class="form-group {{ $errors->has('stdtype') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="stdtype">Student Type</label>
			<div class="col-md-7">
				{{ Form::select('stdtype', array(null=>'--Select Type --','Regular'=>'Regular','In-Service'=>'In-Service','Self-Financed'=>'Self-Financed','Repeater'=>'Repeater'),null, array('class'=>'form-control select2')) }}
				{{ $errors->first('stdtype', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ student-type -->

		<!-- current_semester -->
		<div class="form-group {{ $errors->has('current_semester') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="current_semester">Current Semester</label>
			<div class="col-md-7">
				{{ Form::select('current_semester', array(null=>'--Select Semester--','1'=>'I','2'=>'II','3'=>'III','4'=>'IV','5'=>'V','6'=>'VI','7'=>'VII','8'=>'VIII','9'=>'IX','10'=>'X'),null, array('class'=>'form-control select2')) }}
				{{ $errors->first('current_semester', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ current_semester -->
		
		<!-- programme -->
		<div class="form-group {{ $errors->has('programme_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="programme_id">Programme</label>
			<div class="col-md-7">
				{{ Form::select('programme_id', array(null=>'-- Select Programme --') + $programme_lists , null , array( 'class' => 'form-control select2')) }}
				{{ $errors->first('programme_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ programme -->

		<!-- Back course -->
		<div class="form-group">
			<label class="col-md-3 control-label">Back Course</label>
			<div class="col-md-7">
			@if($mode=='add')
				{{ Form::selectOptMulti($courses, 'back_course[]', 'programme_name', 'module_name','semester_taken' ,'pivot_id', null, array('class'=>'form-control select2')) }}
			@elseif($mode=='edit')
				{{ Form::selectOptMulti($courses, 'back_course[]', 'programme_name', 'module_name','semester_taken','pivot_id', $back_courses, array('class'=>'form-control select2')) }}
			@endif
			</div>
		</div>
		<!-- ./ Back course -->

		<!-- Super back course -->
		<div class="form-group">
			<label class="col-md-3 control-label">Superback Course</label>
			<div class="col-md-7">
			@if($mode=='add')
				{{ Form::selectOptMulti($courses, 'superback_course[]', 'programme_name', 'module_name', 'semester_taken','pivot_id', null, array('class'=>'form-control select2')) }}
			@elseif($mode=='edit')
				{{ Form::selectOptMulti($courses, 'superback_course[]', 'programme_name', 'module_name','semester_taken', 'pivot_id', $superback_courses, array('class'=>'form-control select2')) }}
			@endif
			</div>
		</div>
		<!-- ./ Super back course -->
		
		<!-- blood group -->
		<div class="form-group {{ $errors->has('bloodgroup') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="bloodgroup">Blood Group</label>
			<div class="col-md-7">
				{{ Form::input('text', 'bloodgroup', null, ['id'=>'bloodgroup','class'=>'form-control']) }}
				{{ $errors->first('bloodgroup', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ blood group -->

		<!-- address -->
		<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="address">Address</label>
			<div class="col-md-7">
				{{ Form::textarea('address', null, ['id'=>'address','size'=>'50x4','class'=>'form-control']) }}
				{{ $errors->first('address', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ address -->

		<!-- dzongkhag -->
		<div class="form-group {{ $errors->has('dzongkhag_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="dzongkhag_id">Dzongkhag</label>
			<div class="col-md-7">
				{{ Form::select('dzongkhag_id', array(null=>'-- Select Dzongkhag --') + $dzongkhag_lists , null , array( 'class' => 'form-control select2')) }}
				{{ $errors->first('dzongkhag_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ dzongkhag -->

		<!-- phone -->
		<div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="phone">Phone Number</label>
			<div class="col-md-7">
				{{ Form::input('text', 'phone', null, ['id'=>'phone','class'=>'form-control']) }}
				{{ $errors->first('phone', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ phone -->

		<!-- previous school -->
		<div class="form-group {{ $errors->has('school_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="school_id">Previous High School</label>
			<div class="col-md-7">
				{{ Form::select('school_id', array(null=>'-- Select School --') + $school_lists , null , array( 'class' => 'form-control select2')) }}
				{{ $errors->first('school_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ previous school -->

		<!-- room number -->
		<div class="form-group {{ $errors->has('roomno') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="roomno">Room Number</label>
			<div class="col-md-7">
				{{ Form::input('text', 'roomno', null, ['id'=>'roomno','class'=>'form-control']) }}
				{{ $errors->first('roomno', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ room number -->

		<!-- date enrolled -->
		<div class="form-group {{ $errors->has('enrolled') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="enrolled">Date Enrolled</label>
			<div class="col-md-7">
				{{ Form::input('date', 'enrolled', null, ['id'=>'enrolled','class'=>'form-control']) }}
				{{ $errors->first('enrolled', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ date enrolled -->
		
		<!-- registered -->
		<div class="form-group {{ $errors->has('registered') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="registered">Registered ?</label>
			<div class="col-md-7">
				{{ Form::select('registered', array('No'=>'No','Yes'=>'Yes'), null, array('class'=>'form-control select2')) }}
				{{ $errors->first('registered', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ registered -->

	</div>
	<!-- ./ general tab -->

	@if($mode == 'edit')
	<!-- Remarks tab -->
        <div class="tab-pane" id="tab-remark">
            <!-- Remarks column -->
            <div class="table-responsive" id="remarks-wrapper">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-6">Remarks</th>
                            <th class="col-md-2">By</th>
                            <th class="col-md-2">Updated at</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->remarks as $remark)
                        <tr >
                            <td>{{ $remark->remark }}</td>
                            <td><span class="label label-primary">{{ $student->getStaffName($remark->staff_id) }}</span></td>
                            <td>{{ $remark->updated_at }}</td>
                            <td>
                            	<input type="hidden" name="remark[]" value="{{ $remark->id }}">
                            	<button class="btn btn-danger" type="button" id="remove-remark"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Remarks column -->
        </div>
    <!-- ./ Remarks tab -->
    @endif

</div>
<!-- ./ tabs content -->

<!-- Form Actions -->
<div class="form-group">
	<div class="col-md-offset-3 col-md-7">
		<button type="button" class="btn btn-warning close_popup">Cancel</button>
		<button type="reset" class="btn btn-default">Reset</button>
		<button type="submit" class="btn btn-success">Submit</button>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
	    var wrapper         = $("#remarks-wrapper"); //Fields wrapper
	    
	    $(wrapper).on("click","#remove-remark", function(e){ //user click on remove text
	        e.preventDefault(); //$(this).parent('span').parent('div').remove(); x--;
	        $(this).parent('td').parent('tr').remove(); x--;
	    })
	});

</script>
<!-- ./ form actions -->
{{ Form::close() }}
@stop
