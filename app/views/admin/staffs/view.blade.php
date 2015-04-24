@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
    <li><a href="#tab-course" data-toggle="tab">Courses</a></li>
</ul>
<!-- ./ tabs -->

{{-- View Staff Form --}}
<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		<div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th class="col-md-2">Staff ID</th>
                    <td>{{ $staff->staff_id }}</td>
                </tr>
                 <tr>
                    <th>Name</th>
                    <td>{{ $staff->title.". ".$staff->name }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $staff->position }}</td>
                </tr>
                <tr>
                    <th>Sex</th>
                    <td>{{ $staff->sex }}</td>
                </tr>
                <tr>
                    <th>CID Number</th>
                    <td>{{ $staff->cidno }}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $staff->departmentName($staff->department_id) }}</td>
                </tr>
                <tr>
                    <th>Phone No</th>
                    <td>{{ $staff->phone }}</td>
                </tr>
                <tr>
                    <th>Fax</th>
                    <td>{{ $staff->fax }}</td>
                </tr>

            </table>
        </div>
	</div>
	<!-- ./ general tab -->

    <!-- Course tab -->
    <div class="tab-pane" id="tab-course">
        <!-- Courses taught -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th class="col-md-1">SL No</th>
                        <th class="col-md-4">Modules taught</th>
                        <th>Programme - [Semester] </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=1; ?>
                    @foreach($staff->courseDistinct($staff->id) as $course)
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $staff->courseDetailsById($course->course_id) }}</td>
                        <td>
                            <ul>@foreach($staff->courseProgrammeDetails($course->course_id, $staff->id) as $coupro) 
                                <li>{{ $staff->programmeName($coupro->programme_id)}} - [ {{ $staff->semesterRoman($coupro->semester_taken) }} ]</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Courses taught -->
    </div>
    <!-- ./ Course tab -->
</div>
<!-- ./ tabs content -->

<!-- Form Actions -->
<div class="form-group">
	<div class="col-md-offset-3 col-md-8">
        <a href="{{url('admin/staffs/'.$staff->id. '/edit')}}" class="btn btn-success iframe">
            <span class="fa fa-pencil"></span> EDIT </a>
		<button class="btn btn-success close_popup"> DONE </button>
	</div>
</div>
<!-- ./ form actions -->
@stop
