@extends('layouts.app')
@section('content')
<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Laporan Transaksi
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
						<a class="nav-link active" href="#harian">Harian</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#mingguan">Mingguan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#bulanan">Bulanan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#tahunan">Tahunan</a>
					</li>
				</ul>
				<div class="block-content tab-content overflow-hidden">
					<div class="tab-pane fade fade-left show active" id="harian" role="tabpanel">
						@include('laporan.harian')
					</div>
					<div class="tab-pane fade fade-left" id="mingguan" role="tabpanel">
						@include('laporan.mingguan')
					</div>
					<div class="tab-pane fade fade-left" id="bulanan" role="tabpanel">
						@include('laporan.bulanan')
					</div>
					<div class="tab-pane fade fade-left" id="tahunan" role="tabpanel">
						@include('laporan.tahunan')
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
	var nama='#harian';
	$(document).ready(function(){
		$('#address').val(nama);
		$('[href="'+nama+'"]').click();

		if(nama=='#harian')
		{
			$('#mode').val('harian');
			harian();
		}

		 $('.nav-tabs-block li a.nav-link').click(function(){
            var a=$(this).attr('href');
            $('#address').val(a);
            if(a=='#harian')
            {
            	harian();
            }
            else if(a=='#mingguan')
            {
            	$('#mode').val('mingguan');
            	mingguan();
            }
            else if(a=='#bulanan')
            {
            	$('#mode').val('bulanan');
            	bulanan();
            }
            else if(a=='#tahunan')
            {
            	$('#mode').val('tahunan');
            	tahunan();
            }
         })
	});

</script>
@endpush