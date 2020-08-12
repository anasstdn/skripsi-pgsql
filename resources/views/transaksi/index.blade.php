@extends('layouts.app')
@section('content')
<style>
    .hidden {
        width: 0;
        display: none;
    }
</style>

<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Transaksi Penjualan
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<div class="block">
		<div class="block-header">
		</div>
		<div class="block-content block-content-full">
			<h4 class="font-w400">Filter Pencarian</h4>
			<div class="form-group row">
				<div class="col-sm-2" ><b>Tanggal</b></div>
				<div class="col-md-2" style="text-align:left;">
					<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tanggal_awal" name="tanggal_awal" placeholder="" data-date-format="d-m-Y" value="{{date('d-m-Y',strtotime('-30 days'))}}">
				</div>
				<div class="col-md-2" style="text-align:left;">
					<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tanggal_akhir" name="tanggal_akhir" placeholder="" data-date-format="d-m-Y" value="{{date('d-m-Y')}}">
				</div>
			</div>

			<div class="form-group row">
				<div class="col-sm-2" ><b>No Nota</b></div>
				<div class="col-md-4" style="text-align:left;">
					<input type="text" class="form-control form-control-lg bg-white" id="no_nota" name="no_nota" placeholder="" value="">
				</div>
				<div class="col-md-3" style="text-align:left;">
					<span class="help-block" style="color:red;font-size: 10pt">Contoh : 1234</span>
				</div>
			</div>

			<div class="row" style="margin-bottom: 0.2em">
				<div class="col-md-6 text-right">
					<button class="btn btn-primary btn-md" id="cari">Cari</button>&nbsp&nbsp
					<button class="btn btn-outline-success btn-md" id="reset">Reset</button>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-md-1">
					<a class="btn btn-primary btn-sm" id="tambah" href="#" onclick="show_modal('{{url('transaksi/create')}}')" >Tambah</a>
				</div>
			</div>
			<!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
			<div class="row">
				<div class="col-lg-12" id="table_width">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-vcenter" id="tabel">
							<thead>
								<tr>
									<th class="text-center" style="width: 80px;">No</th>
									<th>Tanggal Transaksi</th>
									<th>No Nota</th>
									<th>Pasir Biasa</th>
									<th>Pasir Gendol</th>
									<th>Abu</th>
									<th>Split 2/3</th>
									<th>Split 1/2</th>
									<th>LPA</th>
									<th>Campur</th>
									<th style="width: 15%;">Aksi</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
  </div>
@endsection

@push('js')
<script type="text/javascript">
	var table;
	$(function() {

		 $('.js-select2:not(.js-select2-enabled)').each((index, element) => {
            let el = jQuery(element);
            el.addClass('js-select2-enabled').select2({
                placeholder: el.data('placeholder') || false,
                width: '100%'
            });
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
			ajax : {
				url:"{{ url('transaksi/get-data') }}",
				data: function (d) {
					return $.extend( {}, d, {
						"tanggal_awal": $("#tanggal_awal").val(),
						"tanggal_akhir": $("#tanggal_akhir").val(),
						"no_nota": $("#no_nota").val(),
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
            {
          name: 'campur',
          data: 'campur',
          render: function (data, type, full, meta) {
            if(data=='Y')
            {
              return "<span class=\"badge badge-success\" style=\"color:white\">Ya</span>";
            }
            else if(data=='N')
            {
               return "<span class=\"badge badge-danger\" style=\"color:white;background-color:red\">Tidak</span>";
            }
          },
          title: 'Campur',
        },
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        language: {
          lengthMenu : '{{ "Menampilkan _MENU_ data" }}',
          zeroRecords : '{{ "Data tidak ditemukan" }}' ,
          info : '{{ "_PAGE_ dari _PAGES_ halaman" }}',
          infoEmpty : '{{ "Data tidak ditemukan" }}',
          infoFiltered : '{{ "(Penyaringan dari _MAX_ data)" }}',
          loadingRecords : '{{ "Memuat data dari server" }}' ,
          processing :    '{{ "Memuat data data" }}',
          sSearchPlaceholder: "Pencarian..",
          lengthMenu: "_MENU_",
          search: "_INPUT_",
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

      });


        $('#cari').click(function(){
         table.ajax.reload( null, false );  
       });

        $('#reset').click(function(){
        	$('#tanggal_awal').val('{{date('d-m-Y',strtotime('-30 days'))}}');
        	$('#tanggal_akhir').val('{{date('d-m-Y')}}');
        	$('#no_nota').val('');
        	table.ajax.reload( null, false );  
        });

	})
</script>
@endpush