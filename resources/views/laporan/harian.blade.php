	<h4 class="font-w400">Filter Pencarian</h4>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Tanggal</b></div>
		<div class="col-md-4" style="text-align:left;">
			<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tanggal" name="tanggal" placeholder="" data-date-format="d-m-Y" value="{{date('d-m-Y')}}">
		</div>
	</div>

	<div class="row" style="margin-bottom: 0.2em">
		<div class="col-md-6 text-right">
			<button class="btn btn-primary btn-md" id="cari">Cari</button>&nbsp&nbsp
			<button class="btn btn-outline-success btn-md" id="reset">Reset</button>
		</div>
	</div>
	<br/>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-vcenter" id="tabel">
			<thead>
				<tr>
					<th rowspan="2" class="align-middle">No</th>
					<th rowspan="2" class="align-middle">Tanggal Transaksi</th>
					<th rowspan="2" class="align-middle">No Nota</th>
					<th colspan="6" style="text-align: center">Jumlah (Meter Kubik)</th>
				</tr>
				<tr>
					<th style="text-align: center">Pasir</th>
					<th style="text-align: center">Gendol</th>
					<th style="text-align: center">Abu</th>
					<th style="text-align: center">Split 1/2</th>
					<th style="text-align: center">Split 2/3</th>
					<th style="text-align: center">LPA</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="3"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tfoot>
		</table>
	</div>

	@push('js')
	<script type="text/javascript">
		function harian()
		{
			var table;

			$('.trash-ck').click(function(){
				if ($('.trash-ck').prop('checked')) {
					document.location = '{{ url("config-id?status=trash") }}';
				} else {
					document.location = '{{ url("config-id") }}';
				}
			});

			table=$('#tabel').DataTable({
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
					url:"{{ url('laporan/harian') }}",
					data: function (d) {
						return $.extend( {}, d, {
							"tanggal": $("#tanggal").val(),
						} );
					}
				},
				columns: [
				{data: 'nomor', name: 'nomor'},
				{data: 'tgl_transaksi', name: 'tgl_transaksi'},
				{data: 'no_nota', name: 'no_nota'},
				{
					name: 'pasir',
					data: 'pasir',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'Pasir Biasa',
				},
				{
					name: 'gendol',
					data: 'gendol',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'Pasir Gendol',
				},
				{
					name: 'abu',
					data: 'abu',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'Abu',
				},
				{
					name: 'split2_3',
					data: 'split2_3',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'Split 2/3',
				},
				{
					name: 'split1_2',
					data: 'split1_2',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'Split 1/2',
				},
				{
					name: 'lpa',
					data: 'lpa',
					render: function (data, type, full, meta) {
						if(data==null)
						{
							return '0';
						}
						else
						{
							return data;
						}
					},
					title: 'LPA',
				},
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
				"footerCallback": function ( row, data, start, end, display ) {
					console.log(data);
					var api = this.api(), data;

					var intVal = function ( i ) {
						return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
						i : 0;
					};


					var pasir = api
					.column( 3 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var gendol = api
					.column( 4 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var abu = api
					.column( 5 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var split2_3 = api
					.column( 6 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var split1_2 = api
					.column( 7 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var lpa = api
					.column( 8 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					$( api.column( 0 ).footer() ).html('<strong>TOTAL</strong>');
					$( api.column( 3 ).footer() ).html('<strong>'+pasir+'</strong>');
					$( api.column( 4 ).footer() ).html('<strong>'+gendol+'</strong>');
					$( api.column( 5 ).footer() ).html('<strong>'+abu+'</strong>');
					$( api.column( 6 ).footer() ).html('<strong>'+split2_3+'</strong>');
					$( api.column( 7 ).footer() ).html('<strong>'+split1_2+'</strong>');
					$( api.column( 8 ).footer() ).html('<strong>'+lpa+'</strong>');
				},
			});

			$('#cari').click(function(){
				table.ajax.reload( null, false );  
			});

			$('#reset').click(function(){
				$('#tanggal').val('{{date('d-m-Y')}}');
				table.ajax.reload( null, false );  
			});
		}
	</script>
	@endpush