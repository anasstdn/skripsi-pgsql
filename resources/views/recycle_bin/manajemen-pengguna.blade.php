	<h4 class="font-w400">Filter Pencarian</h4>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Created At</b></div>
		<div class="col-md-4" style="text-align:left;">
			<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="created_at1" name="created_at" placeholder="" data-date-format="d-m-Y" value="">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Updated At</b></div>
		<div class="col-md-4" style="text-align:left;">
			<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="updated_at1" name="updated_at" placeholder="" data-date-format="d-m-Y" value="">
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Deleted At</b></div>
		<div class="col-md-4" style="text-align:left;">
			<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="updated_at1" name="updated_at" placeholder="" data-date-format="d-m-Y" value="">
		</div>
	</div>

	<div class="row" style="margin-bottom: 0.2em">
		<div class="col-md-6 text-right">
			<button class="btn btn-primary btn-md" id="cari1">Cari</button>&nbsp&nbsp
			<button class="btn btn-outline-success btn-md" id="reset1">Reset</button>&nbsp&nbsp
		</div>
	</div>
	<br/>
	<div class="row">
		<div class="col-md-12 text-right">
			<button class="btn btn-info btn-md" onclick="restore('{{url('recycle-bin/restore-all-manajemen-pengguna')}}')">Restore All Data</button>&nbsp&nbsp
			<button class="btn btn-danger btn-md" onclick="hapus('{{url('recycle-bin/delete-all-manajemen-pengguna')}}')">Delete All Data</button>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-vcenter" id="tabel1">
			<thead>
			<tr>
				<th class="text-center" style="width: 80px;">No</th>
				<th>Nama</th>
				<th>Username</th>
				<th class="d-none d-sm-table-cell" style="width: 30%;">Email</th>
				<th>Created At</th>
				<th>Updated At</th>
				<th>Deleted At</th>
				<th style="width: 15%;">Aksi</th>
			</tr>
		</thead>
			<tbody>

			</tbody>
		</table>
	</div>
	@push('js')
	<script type="text/javascript">
		function manajemen_pengguna()
		{
			var table;

			$('.trash-ck').click(function(){
				if ($('.trash-ck').prop('checked')) {
					document.location = '{{ url("recycle-bin?status=trash") }}';
				} else {
					document.location = '{{ url("recycle-bin") }}';
				}
			});

			table=$('#tabel1').DataTable({
				pagingType: "full_numbers",
				pageLength: 10,
				lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
				autoWidth: true,
				stateSave: true,
				processing : true,
				serverSide : true,
				bFilter:false,
				bDestroy: true,
				ajax : {
					url:"{{ url('recycle-bin/load-manajemen-pengguna') }}",
					data: function (d) {
						return $.extend( {}, d, {
							"created_at": $("#created_at1").val(),
							"updated_at": $("#updated_at1").val(),
							"deleted_at": $("#deleted_at1").val(),
						} );
					}
				},
				columns: [
				{ data: 'nomor', name: 'nomor',searchable:false,orderable:false },
				{data: 'name', name: 'name'},
				{data: 'username', name: 'username'},
				{data: 'email', name: 'email'},
				{ data: 'created_at', name: 'created_at' },
				{ data: 'updated_at', name: 'updated_at' },
				{ data: 'deleted_at', name: 'deleted_at' },
				{ data: 'action', name: 'action', orderable: false, searchable: false },
				],
				language: {
					lengthMenu : '{{ "Menampilkan _MENU_ data" }}',
					zeroRecords : '{{ "Data tidak ditemukan" }}' ,
					info : '{{ "_PAGE_ dari _PAGES_ halaman" }}',
					infoEmpty : '{{ "Data tidak ditemukan" }}',
					infoFiltered : '{{ "(Penyaringan dari _MAX_ data)" }}',
					loadingRecords : '{{ "Memuat data dari server" }}' ,
					processing :    '{{ "Memuat data data" }}',
					search :        '{{ "Pencarian:" }}',
					paginate : {
						first :     '{{ "<" }}' ,
						last :      '{{ ">" }}' ,
						next :      '{{ ">>" }}',
						previous :  '{{ "<<" }}'
					}
				},
				aoColumnDefs: [{
					bSortable: false,
					aTargets: [-1]
				}],
				iDisplayLength: 5,
				aLengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
				iDisplayLength: -1,
			});

			$('#cari1').click(function(){
				table.ajax.reload( null, false );  
			});

			$('#reset1').click(function(){
				$('#created_at1').val('');
				$('#updated_at1').val('');
				$('#deleted_at1').val('');
				table.ajax.reload( null, false );  
			});
		}
	</script>
	@endpush