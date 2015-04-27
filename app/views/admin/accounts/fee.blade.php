@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')

{{-- View Student Form --}}

<!-- short student's info -->
<div class="tab-content">
    <!-- general -->
	<div class="table-responsive col-md-6">
        <table class="table table-bordered table-hover table-striped">
            <tr>
                <th class="col-md-2">Student Number</th>
                <td class="col-md-4">{{ $student->stdno }}</td>
            </tr>
             <tr>
                <th>Name</th>
                <td>{{ $student->fname. " " .$student->mname. " " .$student->lname }}</td>
            </tr>
            <tr>
                <th>Student Type</th>
                <td>{{ $student->stdtype }}</td>
            </tr>
            <tr>
                <th>Current Semester</th>
                <td>{{ $student->semesterRoman($student->current_semester) }}</td>
            </tr>
            <tr>
                <th>Programme</th>
                <td>{{ $programme_name }}</td>
            </tr>
            <tr>
                <th>Resident Type</th>
                <td>{{ $student->resident }}</td>
            </tr>
            <tr>
                <th>Registered</th>
                <td>@if($student->registered == "No")<span class="label label-danger">No</span> @elseif($student->registered=="Yes")Yes @endif</td>
            </tr>
        </table>
    </div>
    <!-- ./ general -->
    
    <!-- Courses subscribed -->
    <div class="table-responsive col-md-6">
        <table class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th class="col-md-1">Semester</th>
                    <th>Courses Subscribed - [ Type ]</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semesters as $semester)
                <tr>
                    <td class="col-md-1">{{ $student->semesterRoman($semester->semester_taken) }}</td>
                    <td>
                        <ol>
                        @foreach( $student->getCoursesBySemester($semester->semester_taken, $student) as $course) 
                            <li>{{ $course->module_code." - ".$course->module_name }}
                                @if($course->type=='Superback')
                                    <span class="label label-danger">
                                @elseif($course->type=='Back')
                                    <span class="label label-warning"> 
                                @else 
                                    <span class="label label-info"> 
                                @endif
                                {{ $course->type }} </span>
                            </li>
                        @endforeach
                        </ol>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Courses subscribed -->
    @if($student->stdtype=='Repeater') <p class="text-danger">** Please note that repeater shall need to pay tution fees for the regular courses as well!</p> @endif
</div>
<!-- ./ short student's info -->

<!-- Fees form -->
{{ Form::model($account, ['autocomplete'=>'off','class'=>'form-horizontal']) }}
<div class="tab-content col-md-12">        
    <!-- fees paid -->
    <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
        <label class="col-md-3 control-label" for="paid">Registered ?</label>
        <div class="col-md-7">
            {{ Form::select('paid', array(null=>' ','No'=>'No','Yes'=>'Yes'), null, array('class'=>'form-control select')) }}
            {{ $errors->first('paid', '<span class="help-block">:message</span>') }}
        </div>
    </div>
    <!-- ./ fees paid -->

    <!-- receipt number -->
    <div class="form-group {{ $errors->has('receipt_no') ? 'has-error' : '' }}">
        <label class="col-md-3 control-label" for="receipt_no"> Receipt Number </label>
        <div class="col-md-7">
            {{ Form::input('text', 'receipt_no', null, ['id'=>'receipt_no','class'=>'form-control']) }}
            {{ $errors->first('receipt_no', '<span class="help-block">:message</span>') }}
        </div>
    </div>
    <!-- ./ receipt number -->

    <!-- amount -->
    <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
        <label class="col-md-3 control-label" for="amount"> Amount </label>
        <div class="col-md-7">
            {{ Form::input('text', 'amount', null, ['id'=>'amount','class'=>'form-control']) }}
            {{ $errors->first('amount', '<span class="help-block">:message</span>') }}
        </div>
    </div>
    <!-- ./ amount -->
</div>
<!-- ./ Fees form -->

<!-- Form Actions -->
<div class="form-group">
    <div class="col-md-offset-4 col-md-7">
        <button type="button" class="btn btn-warning close_popup">Cancel</button>
        <button type="reset" class="btn btn-default">Reset</button>
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</div>
<!-- ./ Form Actions -->
{{ Form::close() }}
@stop
