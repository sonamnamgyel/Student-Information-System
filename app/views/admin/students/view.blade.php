@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
    <li><a href="#tab-course" data-toggle="tab">Courses</a></li>
    <li><a href="#tab-fee" data-toggle="tab">Fees</a></li>
	<li><a href="#tab-remark" data-toggle="tab">Remarks</a></li>
</ul>
<!-- ./ tabs -->

{{-- View Student Form --}}
{{ Form::model($student, ['class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		<div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th class="col-md-2">Student Number</th>
                    <td class="col-md-8">{{ $student->stdno }}</td>
                </tr>
                 <tr>
                    <th>Name</th>
                    <td>{{ $student->fname. " " .$student->mname. " " .$student->lname }}</td>
                </tr>
                <tr>
                    <th>CID Number</th>
                    <td>{{ $student->cidno }}</td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td>{{ $student->gender }}</td>
                </tr>
                <tr>
                    <th>Date of Birth</th>
                    <td>{{ $student->dob }}</td>
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
                    <th>Department</th>
                    <td>{{ $department_name }}</td>
                </tr>
                <tr>
                    <th>Email Address</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Blood Group</th>
                    <td>{{ $student->bloodgroup }}</td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>{{ $student->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $student->address }}</td>
                </tr>
                <tr>
                    <th>Dzongkhag</th>
                    <td>{{ $student->dzongkhagName($student->dzongkhag_id) }}</td>
                </tr>
                <tr>
                    <th>Previous School</th>
                    <td>{{ $student->schoolName($student->school_id) }}</td>
                </tr>
                <tr>
                    <th>Resident Type</th>
                    <td>{{ $student->resident }}</td>
                </tr>
                <tr>
                    <th>Room Number</th>
                    <td>{{ $student->roomno }}</td>
                </tr>
                <tr>
                    <th>Parent's Name</th>
                    <td>{{ $student->parent_name }}</td>
                </tr>
                <tr>
                    <th>Parent's Occupation</th>
                    <td>{{ $student->parent_occupation }}</td>
                </tr>
                <tr>
                    <th>Parent's Contact No.</th>
                    <td>{{ $student->parent_contactno }}</td>
                </tr>
                <tr>
                    <th>Enrolled on</th>
                    <td>{{ $student->enrolled }}</td>
                </tr>
                <tr>
                    <th>Registered</th>
                    <td>@if($student->registered == "No")<span class="label label-danger">No</span> @elseif($student->registered=="Yes")Yes @endif</td>
                </tr>
                <tr>
                    <th>Created at</th>
                    <td>{{ $student->created_at }}</td>
                </tr>
                <tr>
                    <th>Last Update</th>
                    <td>{{ $student->updated_at }}</td>
                </tr>

            </table>
        </div>
	</div>
	<!-- ./ general tab -->

    <!-- Course tab -->
        <div class="tab-pane" id="tab-course">
            <!-- Courses subscribed -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-1">Semester</th>
                            <th><span class="col-md-5">Courses Subscribed</span><span>Type</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesters as $semester)
                        <tr>
                            <td class="col-md-1">{{ $student->semesterRoman($semester->semester_taken) }}</td>
                            <td>
                                <ol>
                                @foreach( $student->getCoursesBySemester($semester->semester_taken, $student) as $course) 
                                    <span class="col-md-5"><li>{{ $course->module_code." - ".$course->module_name }}</li> </span>
                                        @if($course->type=='Superback') <span class="label label-danger">
                                        @elseif($course->type=='Back')<span class="label label-warning"> 
                                        @else <span class="label label-info"> @endif
                                        {{ $course->type }} </span><br>
                                @endforeach
                                </ol>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Courses subscribed -->
        </div>
    <!-- ./ Course tab -->

    <!-- Remarks tab -->
        <div class="tab-pane" id="tab-remark">
            <!-- Remarks column -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-1">SL No</th>
                            <th class="col-md-7">Remarks</th>
                            <th class="col-md-2">By</th>
                            <th>Updated at</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sl=1; ?>
                        @foreach($student->remarks as $remark)
                        <tr>
                            <td>{{ $sl }}</td>
                            <td>{{ $remark->remark }}</td>
                            <td><span class="label label-primary">{{ $student->getStaffName($remark->staff_id) }}</span></td>
                            <td>{{ $remark->updated_at }}</td>
                        </tr>
                        <?php $sl++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Remarks column -->
        </div>
    <!-- ./ Remarks tab -->

    <!-- Fees tab -->
        <div class="tab-pane" id="tab-fee">
            <!-- Fees column -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <th class="col-md-2">Fees Paid?</th>
                        <td>{{ $fee->paid }}</td>
                    </tr>                    
                    <tr>
                        <th>Receipt Number</th>
                        <td>{{ $fee->receipt_no }}</td>
                    </tr>                    
                    <tr>
                        <th>Amount</th>
                        <td>{{ $fee->amount }}</td>
                    </tr>                    
                    <tr>
                        <th>Last Update</th>
                        <td>{{ $fee->updated_at }}</td>
                    </tr>
                </table>
            </div>
            <!-- Fees column -->
        </div>
    <!-- ./ Fees tab -->
</div>
<!-- ./ tabs content -->

<!-- Form Actions -->
<div class="form-group">
	<div class="col-md-offset-3 col-md-8">
		<a href="{{url('admin/students/'.$student->id. '/edit')}}" class="btn btn-success iframe">
            <span class="fa fa-pencil"></span> EDIT </a>
		<button class="btn btn-success close_popup"> DONE </button>
	</div>
</div>
<!-- ./ form actions -->
{{ Form::close() }}
@stop
