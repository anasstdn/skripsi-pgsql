	<h4 class="font-w400">Filter Pencarian</h4>
	<div class="form-group row">
		<div class="col-sm-2" ><b>Tahun</b></div>
		<div class="col-md-4" style="text-align:left;">
			<select class="form-control" id="tahun1">
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
			<button class="btn btn-primary btn-md" id="cari3">Cari</button>&nbsp&nbsp
			<button class="btn btn-outline-success btn-md" id="reset3">Reset</button>
		</div>
	</div>
	<br/>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-vcenter" id="tabel3">
			<thead>
				<tr>
					<th rowspan="2" style="vertical-align: middle">No</th>
					<th rowspan="2" style="vertical-align: middle">Nama Produk</th>
					<th colspan="12" style="text-align: center">Jumlah (Meter Kubik)</th>
				</tr>
				<tr>
					<th style="text-align: center">Jan</th>
					<th style="text-align: center">Feb</th>
					<th style="text-align: center">Mar</th>
					<th style="text-align: center">Apr</th>
					<th style="text-align: center">Mei</th>
					<th style="text-align: center">Jun</th>
					<th style="text-align: center">Jul</th>
					<th style="text-align: center">Aug</th>
					<th style="text-align: center">Sep</th>
					<th style="text-align: center">Okt</th>
					<th style="text-align: center">Nov</th>
					<th style="text-align: center">Des</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>

	@push('js')
	<script type="text/javascript">
		function tahunan()
		{
			var table;

			$('.trash-ck').click(function(){
				if ($('.trash-ck').prop('checked')) {
					document.location = '{{ url("config-id?status=trash") }}';
				} else {
					document.location = '{{ url("config-id") }}';
				}
			});

			table=$('#tabel3').DataTable({
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
					url:"{{ url('laporan/tahunan') }}",
					data: function (d) {
						return $.extend( {}, d, {
							"tahun": $("#tahun1").val(),
						} );
					}
				},
				columns: [
				{data: 'nomor', name: 'nomor'},
				{data: 'produk', name: 'produk'},
				{
					name: 'jan',
					data: 'jan',
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
					title: 'Jan',
				},
				{
					name: 'feb',
					data: 'feb',
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
					title: 'Feb',
				},
				{
					name: 'mar',
					data: 'mar',
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
					title: 'Mar',
				},
				{
					name: 'apr',
					data: 'apr',
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
					title: 'Apr',
				},
				{
					name: 'mei',
					data: 'mei',
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
					title: 'Mei',
				},
				{
					name: 'jun',
					data: 'jun',
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
					title: 'Jun',
				},
				{
					name: 'jul',
					data: 'jul',
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
					title: 'Jul',
				},
				{
					name: 'aug',
					data: 'aug',
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
					title: 'Aug',
				},
				{
					name: 'sep',
					data: 'sep',
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
					title: 'sep',
				},
				{
					name: 'okt',
					data: 'okt',
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
					title: 'Okt',
				},
				{
					name: 'nov',
					data: 'nov',
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
					title: 'Nov',
				},
				{
					name: 'des',
					data: 'des',
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
					title: 'Des',
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
				
			});

			$('#cari3').click(function(){
				table.ajax.reload( null, false );  
			});

			$('#reset3').click(function(){
				$('#tahun1').val('{{date('Y')}}').trigger('change');
				table.ajax.reload( null, false );  
			});
		}
	</script>
	@endpush