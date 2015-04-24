@extends('admin.layouts.modal')
<script src="{{ url('adminlte/js/jquery.min.js') }}"></script>
{{-- Content --}}
@section('content')
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
</ul>
<!-- ./ tabs -->

{{-- Create Course Form --}}
{{ Form::model($course, ['autocomplete'=>'off','class'=>'form-horizontal']) }}

<!-- Tabs Content -->
<div class="tab-content">
	<!-- General tab -->
	<div class="tab-pane active" id="tab-general">
		
		<!-- course code -->
		<div class="form-group {{  $errors->has('module_code') ? 'has-error' : '' }}">
			<label class="col-md-2 control-label" for="module_code"> Module Code </label>
			<div class="col-md-8">
				{{ Form::input('text', 'module_code', null, ['id'=>'module_code','class'=>'form-control']) }}
				{{ $errors->first('module_code', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ course code -->
		
		<!-- course name -->
		<div class="form-group {{  $errors->has('module_name') ? 'has-error' : '' }}">
			<label class="col-md-2 control-label" for="module_name">Module Name</label>
			<div class="col-md-8">
				{{ Form::input('text', 'module_name', null, ['id'=>'module_name','class'=>'form-control']) }}
				{{ $errors->first('module_name', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ course name -->

		<!-- credits -->
		<div class="form-group {{  $errors->has('credits') ? 'has-error' : '' }}">
			<label class="col-md-2 control-label" for="credits">Credits</label>
			<div class="col-md-8">
				{{ Form::input('text', 'credits', null, ['id'=>'credits','class'=>'form-control']) }}
				{{ $errors->first('credits', '<span class="help-block">:message</span>') }}
			</div>
		</div>
		<!-- ./ credits -->
		<!-- group -->
		<div class="form-group">
			<!-- label group -->
			<div>
				<table>
					<tr>
						<td class="col-md-3"><label class="control-label"> Programme </label></td>
						<td class="col-md-2"><label class="control-label"> Semester </label></td>
						<td class="col-md-2"><label class="control-label"> Elective? </label></td>
						<td class="col-md-2"><label> Select for this semester? </label></td>
						<td class="col-md-3"><label class="control-label"> Tutor </label></td>
						<td class="col-md-1"><label class="control-label"> <button type="button" class="btn btn-success" id="add-group"><i class="fa fa-plus fa-lg"></i></button> </label></td>
					</tr>
				</table>
			</div>
			<!-- ./ label group -->

			<!-- group wrapper -->
			<div id="group-wrapper">
				<table>
				@if($mode == "add")
					<tr>
						<td class="col-md-3 {{ $errors->has('programme_id.0') ? 'has-error' : '' }}">
							@if($pid!=null)
								{{ Form::select('programme_id[]', array(null=>'-- Select programme --') + $programme_lists , $pid , array( 'class' => 'form-control select')) }}
							@else
								{{ Form::select('programme_id[]', array(null=>'-- Select programme --') + $programme_lists , null , array( 'class' => 'form-control select')) }}
							@endif
						</td>
						<td class="col-md-2 {{ $errors->has('semester_taken.0') ? 'has-error' : '' }}">
							{{ Form::select('semester_taken[]', array(null=>'-- Select Semester --','1'=>'I','2'=>'II','3'=>'III','4'=>'IV','5'=>'V','6'=>'VI','7'=>'VII','8'=>'VIII','9'=>'IX','10'=>'X'),null, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-2 {{ $errors->has('elective.0') ? 'has-error' : '' }}">
							{{ Form::select('elective[]', array(null=>'','No'=>'No','Yes'=>'Yes'), null, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-2 {{ $errors->has('selected.0') ? 'has-error' : '' }}">
							{{ Form::select('selected[]', array(null=>'','Yes'=>'Yes','No'=>'No'), null, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-3 {{ $errors->has('staff_id.0') ? 'has-error' : '' }}">
							{{ Form::selectOpt($staffs, 'staff_id[]', 'department_name', 'name', 'id', null, '--Select Tutor--',array('class'=>'form-control select2')) }}
						</td>
						<td class="col-md-1">
							<button class="btn btn-danger" type="button" id="remove-group"><i class="fa fa-minus"></i></button>
						</td>
					</tr>
				@elseif($mode == "edit")
					@foreach($course->programmes as $key => $p)
					<tr>
						<td class="col-md-3 {{ $errors->has('programme_id.'.$key) ? 'has-error' : '' }}" >
							{{ Form::select('programme_id[]', array(null=>'-- Select programme --') + $programme_lists , $p->id , array( 'class' => 'form-control select2', 'disabled')) }}
							<input type="hidden" name="programme_id[]" value="{{ $p->id }}">
							<input type="hidden" name="pivot_id[]" value="{{ $p->pivot->pivot_id }}" />
						</td>
						<td class="col-md-2 {{ $errors->has('semester_taken.'.$key) ? 'has-error' : '' }}">
							{{ Form::select('semester_taken[]', array(null=>'-- Select Semester --','1'=>'I','2'=>'II','3'=>'III','4'=>'IV','5'=>'V','6'=>'VI','7'=>'VII','8'=>'VIII','9'=>'IX','10'=>'X'),$p->pivot->semester_taken, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-2 {{ $errors->has('elective.'.$key) ? 'has-error' : '' }}">
							{{ Form::select('elective[]', array(null=>'','No'=>'No','Yes'=>'Yes'),$p->pivot->elective, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-2 {{ $errors->has('selected.'.$key) ? 'has-error' : '' }}">
							{{ Form::select('selected[]', array(null=>'','Yes'=>'Yes','No'=>'No'),$p->pivot->selected, array('class'=>'form-control select')) }}
						</td>
						<td class="col-md-3 {{ $errors->has('staff_id.'.$key) ? 'has-error' : '' }}">
							{{ Form::selectOpt($staffs, 'staff_id[]', 'department_name', 'name', 'id',$p->pivot->staff_id, '--Select Tutor--',array('class'=>'form-control select2')) }}
						</td>
						<td class="col-md-1">
							<button class="btn btn-danger" type="button" id="remove-group"><i class="fa fa-minus"></i></button>
						</td>
					</tr>
					@endforeach
				@endif
				</table>
			</div>
			<!-- ./ group wrapper -->
		</div>	
		<!-- ./ group -->
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
	    var wrapper         = $("#group-wrapper"); //Fields wrapper
	    var add_button      = $("#add-group"); //Add button ID
	    
	    var x = 1; //initlal text box count
	    $(add_button).click(function(e){ //on add input button click
	        e.preventDefault();
	        if(x < max_fields){ //max input box allowed
	            x++; //text box increment
	            $(wrapper).append(
	            	'<table><tr>'+
	            		'@if($mode=="edit")<input type="hidden" name="pivot_id[]" value="" />@endif'+
						'<td class="col-md-3">{{ Form::select("programme_id[]", array(null=>"-- Select programme --") + $programme_lists , null , array( "class" => "form-control select2")) }}</td>'+
						'<td class="col-md-2">{{ Form::select("semester_taken[]", array(null=>"-- Select Semester --","1"=>"I","2"=>"II","3"=>"III","4"=>"IV","5"=>"V","6"=>"VI","7"=>"VII","8"=>"VIII","9"=>"IX","10"=>"X"),null, array("class"=>"form-control select")) }}</td>'+
						'<td class="col-md-2">{{ Form::select("elective[]", array(null=>"","No"=>"No","Yes"=>"Yes"),null, array("class"=>"form-control select")) }}</td>'+
						'<td class="col-md-2">{{ Form::select("selected[]", array(null=>"","Yes"=>"Yes","No"=>"No"),null, array("class"=>"form-control select")) }}</td>'+
						'<td class="col-md-3">{{ Form::selectOpt($staffs, "staff_id[]", "department_name", "name", "id", null, "--Select Tutor--",array("class"=>"form-control select2")) }}</td>'+
						'<td class="col-md-1"><button class="btn btn-danger" type="button" id="remove-group"><i class="fa fa-minus"></i></button></td>'+
					'</tr></table>'
	            );
	        }
	    });
	    
	    $(wrapper).on("click","#remove-group", function(e){ //user click on remove text
	        e.preventDefault();
	        $(this).parent('td').parent('tr').remove(); x--;
	    })
	});

</script>
{{ Form::close() }}
@stop
