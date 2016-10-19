@extends("la.layouts.app")

@section("contentheader_title", trans('laraadmin.resources.backups.title'))
@section("contentheader_description", trans('laraadmin.resources.backups.listing'))
@section("section", trans('laraadmin.resources.backups.section'))
@section("sub_section", trans('laraadmin.resources.backups.listing'))
@section("htmlheader_title", trans('laraadmin.resources.backups.title'))
@section("headerElems")
@la_access("Backups", "create")
	<button class="btn btn-success btn-sm pull-right" id="CreateBackup">@lang('laraadmin.resources.backups.create')</button>
@endla_access
@endsection

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="box box-success">
	<!--<div class="box-header"></div>-->
	<div class="box-body">
		<table id="example1" class="table table-bordered">
		<thead>
		<tr class="success">
			@foreach( $listing_cols as $col )
			<th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
			@endforeach
			@if($show_actions)
			<th>Actions</th>
			@endif
		</tr>
		</thead>
		<tbody>
			
		</tbody>
		</table>
	</div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
	$("#example1").DataTable({
		processing: true,
        serverSide: true,
        ajax: "{{ url(config('laraadmin.adminRoute') . '/backup_dt_ajax') }}",
		language: {
			lengthMenu: "_MENU_",
			search: "_INPUT_",
			searchPlaceholder: "Search"
		},
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});
	
	$("#CreateBackup").on("click", function() {
		$.ajax({
			url: "{{ url(config('laraadmin.adminRoute') . '/create_backup_ajax') }}",
			method: 'POST',
			beforeSend: function() {
				$("#CreateBackup").html('<i class="fa fa-refresh fa-spin"></i> @lang('laraadmin.resources.backups.creating_backup')');
			},
			headers: {
		    	'X-CSRF-Token': $('input[name="_token"]').val()
    		},
			success: function( data ) {
				if(data.status == "success") {
					$("#CreateBackup").html('<i class="fa fa-check"></i> @lang('laraadmin.resources.backups.backup_created')');
					$('body').pgNotification({
						style: 'circle',
						title: '@lang('laraadmin.resources.backups.backup_creation')',
						message: data.message,
						position: "top-right",
						timeout: 0,
						type: "success",
						thumbnail: '<img width="40" height="40" style="display: inline-block;" src="{{ asset('la-assets/img/laraadmin_logo_white.png') }}" data-src="assets/img/profiles/avatar.jpg" data-src-retina="assets/img/profiles/avatar2x.jpg" alt="">'
					}).show();
					setTimeout(function() {
						window.location.reload();
					}, 1000);
				} else {
					$("#CreateBackup").html('@lang('laraadmin.resources.backups.create')');
					$('body').pgNotification({
						style: 'circle',
						title: '@lang('laraadmin.resources.backups.creating_backup_failed')',
						message: data.message,
						position: "top-right",
						timeout: 0,
						type: "danger",
						thumbnail: '<img width="40" height="40" style="display: inline-block;" src="{{ asset('la-assets/img/laraadmin_logo_white.png') }}" data-src="assets/img/profiles/avatar.jpg" data-src-retina="assets/img/profiles/avatar2x.jpg" alt="">'
					}).show();
					console.error(data.output);
				}
			}
		});
	});
});
</script>
@endpush
