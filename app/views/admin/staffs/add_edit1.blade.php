@extends('admin.layouts.modal')
<script  src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
</ul>
<!-- ./ tabs -->

{{-- Create Tutor Form --}}
{{ Form::model($tutor, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- tutor id -->
		<div class="form-group {{  $errors->has('tutor_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="tutor_id"> Tutor ID </label>
			<div class="col-md-8">
				{{ Form::input('text', 'tutor_id', null, ['id'=>'tutor_id','class'=>'form-control']) }}
				{{ $errors->first('tutor_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ tutor id -->

		<!-- name -->
		<div class="form-group {{  $errors->has('name') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="name">Full Name</label>
			<div class="col-md-8">
				{{ Form::input('text', 'name', null, ['id'=>'name','class'=>'form-control']) }}
				{{ $errors->first('name', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ name -->
		
		<!-- position -->
		<div class="form-group {{  $errors->has('position') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="position">Position</label>
			<div class="col-md-8">
				{{ Form::input('text', 'position', null, ['id'=>'position','class'=>'form-control']) }}
				{{ $errors->first('position', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ position -->
		
		<!-- sex -->
		<div class="form-group {{  $errors->has('sex') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="sex">Sex</label>
			<div class="col-md-8">
				{{ Form::radio('sex', 'M', (Input::old('sex')== 'M'), array('id'=>'male')) }} Male <br>
				{{ Form::radio('sex', 'F', (Input::old('sex')== 'F'), array('id'=>'female')) }} Female
				{{ $errors->first('sex', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ sex -->

		<!-- department -->
		<div class="form-group {{  $errors->has('department_id') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="department_id">Department</label>
			<div class="col-md-6">
				{{ Form::select('department_id', array(null=>'-- Select Department --') + $department_lists , null , array( 'class' => 'form-control select2')) }}
				{{ $errors->first('department_id', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ department -->

		<!-- course -->
		<div class="form-group {{  $errors->has('course_id[]') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="course_id[]">Course</label>
			<div id="courses-wraper" class="col-md-6">
				@if($mode=='add')
				<table>
				    <tr> 
				        <td>{{ Form::select('course_id[]', array(null=>'-- Select Course --') + $course_lists , null , array( 'class' => 'form-control select')) }}</td>
				        <td><button type="button" class="btn btn-success" id="add-courses"><i class="fa fa-plus fa-lg"></i></button></td>
				    </tr>
				</table>
				@elseif($mode=='edit')
				<table>
					@foreach($tutor->courses as $course)
					<tr>
						<td><label><input type="hidden" name="course_id[]" value="{{ $course->id }}"> <i>{{ $course->course_name }} </i></label> </td>
						<td> <button class="btn btn-xs btn-danger" type="button" id="remove-course"><i class="fa fa-close"></i></button> </td>
					</tr>
					@endforeach
					<tr>
						<td colspan="2"> Add <button type="button" class="btn btn-success" id="add-courses"><i class="fa fa-plus fa-lg"></i></button></td>
					</tr>
				</table>
				@endif
				{{ $errors->first('course_id', '<span class="help-block">:message</span>') }}                 
			</div>
		</div>

		<!-- ./ course -->

		<!-- phone -->
		<div class="form-group {{  $errors->has('phone') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="phone">Phone Number</label>
			<div class="col-md-8">
				{{ Form::input('text', 'phone', null, ['id'=>'phone','class'=>'form-control', 'placeholder'=>'Optional']) }}
				{{ $errors->first('phone', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ phone -->

		<!-- fax -->
		<div class="form-group {{  $errors->has('fax') ? 'has-error' : '' }}">
			<label class="col-md-3 control-label" for="fax">Fax Number</label>
			<div class="col-md-8">
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

<script type="text/javascript">
                    
    $(document).ready(function() {
	    var max_fields      = 10; //maximum input boxes allowed
	    var wrapper         = $("#courses-wraper"); //Fields wrapper
	    var add_button      = $("#add-courses"); //Add button ID
	    
	    var x = 1; //initlal text box count
	    $(add_button).click(function(e){ //on add input button click
	        e.preventDefault();
	        if(x < max_fields){ //max input box allowed
	            x++; //text box increment
	            $(wrapper).append('<tr>'+
	            	'<td>{{ Form::select("course_id[]", array(null=>"-- Select Course --") + $course_lists , null , array( "class" => "form-control select")) }}</td>'+
	            	'<td><button class="btn btn-danger" type="button" id="remove-course"><i class="fa fa-minus"></i></button></td>'+
	            	'</tr>'
	            );
	        }
	    });
	    
	    $(wrapper).on("click","#remove-course", function(e){ //user click on remove text
	        e.preventDefault(); //$(this).parent('span').parent('div').remove(); x--;
	        $(this).parent('td').parent('tr').remove(); x--;
	    })
	});

	function getSearchParameters() {
	      var prmstr = window.location.search.substr(1);
	      return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
	}

	function transformToAssocArray( prmstr ) {
	    var params = {};
	    var prmarr = prmstr.split("&");
	    for ( var i = 0; i < prmarr.length; i++) {
	        var tmparr = prmarr[i].split("=");
	        params[tmparr[0]] = tmparr[1];
	    }
	    return params;
	}

	var params = getSearchParameters();
    
</script>



{{ Form::close() }}
@stop
