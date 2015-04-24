@extends('admin.layouts.modal')

{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
    <li><a href="#tab-course" data-toggle="tab">Course</a></li>
</ul>
<!-- ./ tabs -->

{{-- View Tutor Form --}}
{{ Form::model($tutor, ['class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		<div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th class="col-md-2">Tutor ID</th>
                    <td>{{ $tutor->tutor_id }}</td>
                </tr>
                 <tr>
                    <th>Name</th>
                    <td>{{ $tutor->name }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $tutor->position }}</td>
                </tr>
                <tr>
                    <th>Sex</th>
                    <td>@if($tutor->sex == 'M') Male @elseif($tutor->sex == 'F') Female @endif</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $department_name }}</td>
                </tr>
                <tr>
                    <th>Phone No</th>
                    <td>{{ $tutor->phone }}</td>
                </tr>
                <tr>
                    <th>Fax</th>
                    <td>{{ $tutor->fax }}</td>
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
                            <th class="col-md-2">Semester</th>
                            <th>Courses Taught</th>
                            <th>Department</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tutor->courses as $course)
                        <tr>
                            <td> {{ $course->semester_taken }} </td>
                            <td> {{ $course->course_code }} - {{ $course->course_name }} </td>
                            <td> {{ Department::find($course->department_id)->department_name }} </td>
                        </tr>
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
        <a href="{{url('admin/tutors/'.$tutor->id. '/edit')}}" class="btn btn-success iframe">
            <span class="fa fa-pencil"></span> EDIT </a>
		<button class="btn btn-success close_popup"> DONE </button>
	</div>
</div>
<!-- ./ form actions -->
{{ Form::close() }}
@stop
