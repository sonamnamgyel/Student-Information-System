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
			<a href="{{ url('admin/programmes/add') }}" class="btn btn-primary btn-sm iframe">
				<span class="fa fa-plus fa-lg"></span> Add
			</a>
			<a href="#" onClick="oTable.fnReloadAjax()" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></a>
		</div>
		<i class="fa fa-star-o"></i>
		<h3 class="box-title">Bachelor Degree Programme</h3>
	</div>
	<div class="box-body table-responsive">
		<table id="programmes" class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th class="col-md-1">{{{ Lang::get('admin/programmes/table.slno') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/programmes/table.code') }}}</th>
					<th class="col-md-3">{{{ Lang::get('admin/programmes/table.name') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/programmes/table.department') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/programmes/table.tutors') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/programmes/table.students') }}}</th>
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
	oTable = $('#programmes').dataTable( {
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
		"sAjaxSource": "{{ URL::to('admin/programmes/data') }}",
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