@php
$arr_bulan=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
@endphp
	<h4 class="font-w400">Filter Pencarian</h4>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Bulan / Tahun</b></div>
		<div class="col-md-2" style="text-align:left;">
			<select class="form-control" id="bulan">
				@foreach($arr_bulan as $key=>$val)
				@if($key+1 == date('m'))
				<option value="{{$key+1}}" selected>{{$val}}</option>
				@else
				<option value="{{$key+1}}">{{$val}}</option>
				@endif
				@endforeach
			</select>
		</div>
		<div class="col-md-2" style="text-align:left;">
			<select class="form-control" id="tahun">
				<?php $start=2017?>
				<?php for($start;$start<=date('Y');$start++)
				{?>
					@if($start==date('Y'))
					<option value="{{$start}}" selected="">{{$start}}</option>
					@else
					<option value="{{$start}}">{{$start}}</option>
					@endif
					<?php }?>                
				</select>
			</div>
	</div>

	<div class="row" style="margin-bottom: 0.2em">
		<div class="col-md-6 text-right">
			<button class="btn btn-primary btn-md" id="cari2">Cari</button>&nbsp&nbsp
			<button class="btn btn-outline-success btn-md" id="reset2">Reset</button>
		</div>
	</div>
	<br/>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-vcenter" id="tabel2">
			<thead>
				<tr>
					<th rowspan="2" class="align-middle">No</th>
					<th rowspan="2" class="align-middle">Tanggal Transaksi</th>
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
					<td colspan="2"></td>
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
		function bulanan()
		{
			var table;

			$('.trash-ck').click(function(){
				if ($('.trash-ck').prop('checked')) {
					document.location = '{{ url("config-id?status=trash") }}';
				} else {
					document.location = '{{ url("config-id") }}';
				}
			});

			table=$('#tabel2').DataTable({
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
					url:"{{ url('laporan/bulanan') }}",
					data: function (d) {
						return $.extend( {}, d, {
							"bulan": $("#bulan").val(),
							"tahun": $("#tahun").val(),
						} );
					}
				},
				columns: [
				{data: 'nomor', name: 'nomor'},
				{data: 'tgl_transaksi', name: 'tgl_transaksi'},
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
					.column( 2 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var gendol = api
					.column( 3 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var abu = api
					.column( 4 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var split2_3 = api
					.column( 5 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var split1_2 = api
					.column( 6 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					var lpa = api
					.column( 7 )
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );

					$( api.column( 0 ).footer() ).html('<strong>TOTAL</strong>');
					$( api.column( 2 ).footer() ).html('<strong>'+pasir+'</strong>');
					$( api.column( 3 ).footer() ).html('<strong>'+gendol+'</strong>');
					$( api.column( 4 ).footer() ).html('<strong>'+abu+'</strong>');
					$( api.column( 5 ).footer() ).html('<strong>'+split2_3+'</strong>');
					$( api.column( 6 ).footer() ).html('<strong>'+split1_2+'</strong>');
					$( api.column( 7 ).footer() ).html('<strong>'+lpa+'</strong>');
				},
			});

			$('#cari2').click(function(){
				table.ajax.reload( null, false );  
			});

			$('#reset2').click(function(){
				$('#bulan').val('{{ltrim(date('m'), '0')}}').trigger('change');
				$('#tahun').val('{{date('Y')}}').trigger('change');
				table.ajax.reload( null, false );  
			});
		}
	</script>
	@endpush