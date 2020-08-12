@extends('layouts.app')
@section('content')

<style>
	img.rounded2 {
		object-fit: cover;
		border-radius: 10%;
		height: auto;
		width: 100px;
	}
</style>

<div class="bg-image" style="background-image: url('{{asset('oneui/')}}/src/assets/media/photos/photo8@2x.jpg');">
	<div class="bg-black-50">
		<div class="content content-full text-center">
			<div class="my-3">
				<a class="img-link" href="{{url('profile')}}">
					@if(isset($foto_profile) && $foto_profile->foto!==null)
					<img class="rounded2 img-avatar" src="{{asset('images/')}}/profile/{{$foto_profile->foto}}" alt="">
					@else
					<img class="img-avatar img-avatar96 img-avatar-thumb" src="{{asset('oneui/')}}/src/assets/media/avatars/avatar13.jpg" alt="">
					@endif
				</a>
			</div>
			<?php 
			$id=\Auth::user()->profile_id!==null?\Auth::user()->profile_id:\Auth::user()->id;
			?>
			{{-- <h1 class="h2 text-white mb-0">John Parker</h1> --}}
			<br/><a class="btn btn-primary mb-5 px-20" href="{{url('profile/edit/')}}/{{$id}}">
				<i class="fa fa-pencil-alt"></i> Ubah Profil
			</a>
			{{-- <span class="text-white-75">UI Designer</span> --}}
		</div>
	</div>
</div>

<div class="content">
	<div class="block">
		<div class="col-lg-12">
			<!-- Block Tabs Animated Slide Left -->
			<div class="block">
				<ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" href="#informasi_pribadi">Informasi Pribadi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#informasi_kepegawaian">Informasi Kepegawaian</a>
					</li>
					{{-- <li class="nav-item ml-auto">
						<a class="nav-link" href="#btabs-animated-slideleft-settings">
							<i class="si si-settings"></i>
						</a>
					</li> --}}
				</ul>
				<div class="block-content tab-content overflow-hidden">
					<div class="tab-pane fade fade-left show active" id="informasi_pribadi" role="tabpanel">
						<h4 class="font-w400">Informasi Pribadi</h4>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Nama Depan</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->nama_depan)?\Auth::user()->profile->nama_depan:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Nama Belakang</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->nama_belakang)?\Auth::user()->profile->nama_belakang:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>NIK</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->nik)?\Auth::user()->profile->nik:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Jenis Kelamin</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->jenis_kelamin->jenis_kelamin)?\Auth::user()->profile->jenis_kelamin->jenis_kelamin:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Agama</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->agama->nama_agama)?\Auth::user()->profile->agama->nama_agama:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Tempat / Tanggal Lahir</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->tempat_lahir) && isset(\Auth::user()->profile->tgl_lahir)?\Auth::user()->profile->tempat_lahir.' / '.date_indo(date('Y-m-d',strtotime(\Auth::user()->profile->tgl_lahir))):'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Status Perkawinan</span>
							</div>
							<div class="col-lg-6 text-left">
								<span>{{isset(\Auth::user()->profile->status_perkawinan->status_perkawinan)?\Auth::user()->profile->status_perkawinan->status_perkawinan:'-'}}</span>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Alamat KTP</span>
							</div>
							<div class="col-lg-6 text-left">
								<address>
									{{isset(\Auth::user()->profile->alamat_ktp)?\Auth::user()->profile->alamat_ktp:'-'}}<br/>
									<?php 
									if(!empty(\Auth::user()->profile->id_kelurahan_ktp))
									$kelurahan_ktp=\App\Models\Kelurahan::find(\Auth::user()->profile->id_kelurahan_ktp);
									?>
									{{isset($kelurahan_ktp->nama_kelurahan)?$kelurahan_ktp->nama_kelurahan:'-'}},&nbsp{{isset($kelurahan_ktp->kecamatan->nama_kecamatan)?$kelurahan_ktp->kecamatan->nama_kecamatan:'-'}},&nbsp{{isset($kelurahan_ktp->kecamatan->kabupaten->nama_kabupaten)?$kelurahan_ktp->kecamatan->kabupaten->nama_kabupaten:'-'}},&nbsp{{isset($kelurahan_ktp->kecamatan->kabupaten->provinsi->nama_provinsi)?$kelurahan_ktp->kecamatan->kabupaten->provinsi->nama_provinsi:'-'}} &nbsp&nbsp-&nbsp&nbsp{{isset($kelurahan_ktp->kodepos)?$kelurahan_ktp->kodepos:'-'}}
									<br/>
								</address>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Alamat Domisili</span>
							</div>
							<div class="col-lg-6 text-left">
								<address>
									{{isset(\Auth::user()->profile->alamat_domisili)?\Auth::user()->profile->alamat_domisili:'-'}}<br/>
									<?php 
									if(!empty(\Auth::user()->profile->id_kelurahan_domisili))
									$kelurahan_domisili=\App\Models\Kelurahan::find(\Auth::user()->profile->id_kelurahan_domisili);
									?>
									{{isset($kelurahan_domisili->nama_kelurahan)?$kelurahan_domisili->nama_kelurahan:'-'}},&nbsp{{isset($kelurahan_domisili->kecamatan->nama_kecamatan)?$kelurahan_domisili->kecamatan->nama_kecamatan:'-'}},&nbsp{{isset($kelurahan_domisili->kecamatan->kabupaten->nama_kabupaten)?$kelurahan_domisili->kecamatan->kabupaten->nama_kabupaten:'-'}},&nbsp{{isset($kelurahan_domisili->kecamatan->kabupaten->provinsi->nama_provinsi)?$kelurahan_domisili->kecamatan->kabupaten->provinsi->nama_provinsi:'-'}} &nbsp&nbsp-&nbsp&nbsp{{isset($kelurahan_domisili->kodepos)?$kelurahan_domisili->kodepos:'-'}}
									<br/>
								</address>
							</div>
						</div>
						<div class="form-group row col-md-12">
							<div class="col-lg-2 text-right">
								<span>Kontak</span>
							</div>
							<div class="col-lg-6 text-left">
								<address>
									<abbr title="Phone"></abbr> {{isset(\Auth::user()->profile->no_telp)?\Auth::user()->profile->no_telp:'-'}}
								</address>
							</div>
						</div>
					</div>
					<div class="tab-pane fade fade-left" id="informasi_kepegawaian" role="tabpanel">
						<h4 class="font-w400">Profile Content</h4>
						<p>Content slides in to the left..</p>
					</div>
					{{-- <div class="tab-pane fade fade-left" id="btabs-animated-slideleft-settings" role="tabpanel">
						<h4 class="font-w400">Settings Content</h4>
						<p>Content slides in to the left..</p>
					</div> --}}
				</div>
			</div>
			<!-- END Block Tabs Animated Slide Left -->
		</div>
	</div>
</div>
@endsection

@push('js')
<script type="text/javascript">


</script>
@endpush