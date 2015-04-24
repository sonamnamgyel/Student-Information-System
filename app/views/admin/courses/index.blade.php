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
			<a href="{{ url('admin/courses') }}" class="btn btn-primary btn-sm">
				<span class="fa fa-plus fa-eye"></span> View all
			</a>
			
			@if($programme!=null)
			<a href="{{ url('admin/courses/add/'.$programme->id) }}" class="btn btn-primary btn-sm iframe">
			@else
			<a href="{{ url('admin/courses/add') }}" class="btn btn-primary btn-sm iframe">
			@endif
				<span class="fa fa-plus fa-lg"></span> Add 
			</a>
			
			<a href="#" onClick="oTable.fnReloadAjax()" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></a>
		</div>
		<i class="fa fa-book"></i>
		<h3 class="box-title">Courses for @if($programme==null) all the Programme
											@else
												{{{ $programme->programme_name }}} Programme
											@endif</h3>
	</div>
	<div class="box-body table-responsive">
		<table id="courses" class="table table-striped table-hover table-bordered">
			<thead>
				<tr>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.code') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/courses/table.name') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.credits') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.programme') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.sem_taken') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.elective') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.selected') }}}</th>
					<th class="col-md-2">{{{ Lang::get('admin/courses/table.tutor') }}}</th>
					<th class="col-md-1">{{{ Lang::get('admin/courses/table.students') }}}</th>
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
	oTable = $('#courses').dataTable( {
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
		"sAjaxSource": @if($programme!=null) "{{ URL::to('admin/courses/data/'.$programme->id) }}" @else "{{ URL::to('admin/courses/data') }}" @endif,
		"fnDrawCallback": function ( oSettings ) {
			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
		}
	})
});
</script>
@stop

<!-- -->
<!-- "@if($programme==null){{{ URL::to('admin/courses/data') }}}@else {{{ URL::to('admin/courses/data/'.$programme->id) }}} @endif", -->