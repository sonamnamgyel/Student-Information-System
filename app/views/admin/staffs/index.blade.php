@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
<div class="box box-primary">
	<div class="box-header">
		<div class="pull-right box-tools">
			<a href="{{ url('admin/staffs') }}" class="btn btn-primary btn-sm">
				<span class="fa fa-plus fa-eye"></span> View all
			</a>
			
			@if($department!=null)
			<a href="{{ url('admin/staffs/add/'.$department->id) }}" class="btn btn-primary btn-sm iframe">
			@else
			<a href="{{ url('admin/staffs/add') }}" class="btn btn-primary btn-sm iframe">
			@endif
				<span class="fa fa-plus fa-lg"></span> Add 
			</a>

			<a href="#" onClick="oTable.fnReloadAjax()" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></a>
		</div>
		<i class="fa fa-male"></i>
		<h3 class="box-title">Staffs
					@if($department!=null) under {{{ $department->department_name }}} Department
					@endif
		</h3>
	</div>
	</div>
	<div class="box-body table-responsive">
		<table id="staffs" class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th class="col-md-1">{{{ Lang::get('admin/staffs/table.slno') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/staffs/table.staff_id') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/staffs/table.position') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/staffs/table.name') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/staffs/table.sex') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/staffs/table.department') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/staffs/table.course_no') }}}</th>
					<th class="col-md-2">{{{ Lang::get('table.actions') }}}</th>
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
	oTable = $('#staffs').dataTable( {
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
		"sAjaxSource": @if($department!=null) "{{ URL::to('admin/staffs/data/'.$department->id) }}" @else "{{ URL::to('admin/staffs/data') }}" @endif,
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