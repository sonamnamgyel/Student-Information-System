@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div class="box box-primary">
	<div class="box box-primary">
		<div class="box-header">
			<i class="fa fa-graduation-cap"></i>
			<h3 class="box-title">Student Advanced Search</h3>
			<a href="#" onClick="oTable.fnReloadAjax()" class="btn btn-primary btn-sm pull-right btn-sm"><i class="fa fa-refresh"></i></a>
			<button class="btn btn-primary btn-sm pull-right" data-widget='collapse' data-toggle="tooltip" title="Collapse" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
		</div>
		<!-- Advanced Search -->
		<div class="box-body">
			{{ Form::model($student, ['autocomplete'=>'off','class'=>'form-horizontal']) }}
			<!-- Tabs Content -->
			<div >
				<div class="form-group">
					<!-- department -->
					<label class="col-md-2 control-label" for="department">Department </label>
					<div class="col-md-3">
						{{ Form::select('department', array(0=>' -- ')+$departments, $data['department'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ department -->

					<!-- gender -->
					<label class="col-md-2 control-label" for="gender">Gender</label>
					<div class="col-md-3">
						{{ Form::select('gender', array(0=>' -- ','Male'=>'Male','Female'=>'Female'),$data['gender'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ gender -->
				</div>

				<div class="form-group">
					<!-- programme -->
					<label class="col-md-2 control-label" for="programme">Programme</label>
					<div class="col-md-3">
						{{ Form::select('programme', array(0=>' -- ')+$programmes, $data['programme'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ programme -->

					<!-- previous school -->
					<label class="col-md-2 control-label" for="school">Previous School</label>
					<div class="col-md-3">
						{{ Form::select('school', array(0=>' -- ')+$schools, $data['school'], array('class'=>'form-control select2')) }}
					</div>
					<!-- ./ previous school -->
				</div>

				<div class="form-group">
					<!-- student-type -->
					<label class="col-md-2 control-label" for="stdtype">Student Type</label>
					<div class="col-md-3">
						{{ Form::select('stdtype', array(0=>' -- ','Regular'=>'Regular','Self-Financed'=>'Self-Financed','Repeater'=>'Repeater','In-Service'=>'In-Service'),$data['stdtype'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ student-type -->

					<!-- dzongkhag -->
					<label class="col-md-2 control-label" for="dzongkhag">Dzongkhag</label>
					<div class="col-md-3">
						{{ Form::select('dzongkhag', array(0=>' -- ')+$dzongkhags, $data['dzongkhag'], array('class'=>'form-control select2')) }}
					</div>
					<!-- ./ dzongkhag -->
				</div>

				<div class="form-group">
					<!-- semester -->
					<label class="col-md-2 control-label" for="semester">Semester</label>
					<div class="col-md-3">
						{{ Form::select('semester', array(0=>' -- ','1'=>'I','2'=>'II','3'=>'III','4'=>'IV','5'=>'V','6'=>'VI','7'=>'VII','8'=>'VIII','9'=>'IX','10'=>'X'),$data['semester'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ semester -->
					
					<!-- resident type -->
					<label class="col-md-2 control-label" for="resident">Resident Type</label>
					<div class="col-md-3">
						{{ Form::select('resident', array(0=>' -- ','Boarder'=>'Boarder','Day-Scholar'=>'Day-Scholar'),$data['resident'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ resident type -->

				</div>

				<div class="form-group">
					<!-- registered -->
					<label class="col-md-2 control-label" for="registered">Registered</label>
					<div class="col-md-3">
						{{ Form::select('registered', array(0=>' -- ','Yes'=>'Yes','No'=>'No'),$data['registered'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ registered -->

					<!-- module repeat type -->
					<label class="col-md-2 control-label" for="fee">Module Repeat</label>
					<div class="col-md-3">
						{{ Form::select('repeat', array(0=>' -- ','Back'=>'Back','Superback'=>'Superback', 'both'=>'Back/Superback'),$data['repeat'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ module repeat type -->
				</div>
				
				<div class="form-group">
					<!-- fees paid -->
					<label class="col-md-2 control-label" for="fee">Fees paid?</label>
					<div class="col-md-3">
						{{ Form::select('fee', array(0=>' -- ','Yes'=>'Yes','No'=>'No'),$data['fee'], array('class'=>'form-control select')) }}
					</div>
					<!-- ./ fees paid -->
				</div>

			</div>
			<!-- ./ tabs content -->

			<!-- Form Actions -->
			<div class="form-group">
				<div class="col-md-offset-4 col-md-8">
					<a href="{{ url('admin/students')}}"><button type="button" class="btn btn-warning">Cancel</button></a>
					<button type="reset" class="btn btn-default">Reset</button>
					<button type="submit" class="btn btn-success">Search</button>
				</div>
			</div>
			<!-- ./ form actions -->
			{{ Form::close() }}
		</div>
	<!-- ./ Advanced Search -->
	</div>
	
	<div class="box-body table-responsive">
		<table id="students" class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.slno') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.stdno') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/students/table.name') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.gender') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.stdtype') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.sem') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.programme') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.course') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.registered') }}}</th>
					<th class="col-md-1">{{{ Lang::get('table.actions') }}}</th>
				</tr>
			</thead>
	</table>
	</div>
</div>
@stop

{{-- Scripts --}}
@section('scripts')
<script type="text/javascript">
var oTable;
$(document).ready(function() {
	oTable = $('#students').dataTable( {
		"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
		"aoColumnDefs": [
		{ "bSearchable": false, "bSortable": false, "aTargets": [ 0 ] }
		],
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
		},
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "{{ URL::to('admin/students/details',$data) }}",
		"fnDrawCallback": function ( oSettings ) {
			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
		},
		"fnRowCallback" : function(nRow, aData, iDisplayIndex){
			var oSettings = oTable.fnSettings();
			$("td:first",nRow).html(oSettings._iDisplayStart+iDisplayIndex+1);
			return nRow;
		}
	})
});
</script>
@stop