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
			<a href="{{url('admin/students/search')}}" class="btn btn-primary btn-sm">
				<span class="fa fa-search"></span> Advanced Search </a>
			<a href="{{url('admin/students/add')}}" class="btn btn-primary btn-sm iframe">
				<span class="fa fa-plus fa-lg"></span> Add </a>
			<a href="#" onClick="oTable.fnReloadAjax()" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></a>
		</div>
		<i class="fa fa-graduation-cap"></i>
		<h3 class="box-title">Students</h3>
	</div>
	<div class="box-body table-responsive">
		<table id="students" class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.slno') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.stdno') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/students/table.name') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.sex') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.stdtype') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.sem') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.programme') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/students/table.registered') }}}</th>
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
		"sAjaxSource": "{{ URL::to('admin/students/data') }}",
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