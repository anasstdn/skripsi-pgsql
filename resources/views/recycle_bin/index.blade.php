@extends('layouts.app')
@section('content')
<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Recycle Bin
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<div class="block">
		<div class="col-lg-12">
			<input type="text" style="display: none" id="address" value="">
			<input type="text" class="form-control" style="display: none" value="" name="mode" id="mode"> 
			<div class="block">
				<ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" href="#config_id">Config ID</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#manajemen_pengguna">Manajemen Pengguna</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#transaksi">Transaksi Penjualan</a>
					</li>
				</ul>
				<div class="block-content tab-content overflow-hidden">
					<div class="tab-pane fade fade-left show active" id="config_id" role="tabpanel">
						@include('recycle_bin.config-id')
					</div>
					<div class="tab-pane fade fade-left" id="manajemen_pengguna" role="tabpanel">
						@include('recycle_bin.manajemen-pengguna')
					</div>
					<div class="tab-pane fade fade-left" id="transaksi" role="tabpanel">
						@include('recycle_bin.transaksi')
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
	var nama='#config_id';
	$(document).ready(function(){
		$('#address').val(nama);
		$('[href="'+nama+'"]').click();

		if(nama=='#config_id')
		{
			$('#mode').val('config_id');
			config_id();
		}

		 $('.nav-tabs-block li a.nav-link').click(function(){
            var a=$(this).attr('href');
            $('#address').val(a);
            if(a=='#config_id')
            {
            	config_id();
            }
            else if(a=='#manajemen_pengguna')
            {
            	$('#mode').val('manajemen_pengguna');
            	manajemen_pengguna();
            }
            else if(a=='#transaksi')
            {
            	$('#mode').val('transaksi');
            	transaksi();
            }
         })
	});

</script>
@endpush