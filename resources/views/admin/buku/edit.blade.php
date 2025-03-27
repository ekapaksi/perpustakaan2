@extends('admin.layout.main')
@section('title', 'Master Buku')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Buku</h3>
                </div>

                <!-- Form start -->
                <form action="{{ route('admin.master.buku.update', $buku->id) }}" 
                      method="POST" 
                      id="bukuForm" 
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <!-- Kolom 1 -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="judul_buku">Judul Buku</label>
                                    <textarea class="form-control" rows="1" 
                                              placeholder="Isikan judul buku." 
                                              style="resize: none" 
                                              id="judul_buku" 
                                              name="judul_buku" required>{{ $buku->judul_buku }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="id_kategori">Kategori Buku</label>
                                    <select class="form-control select2" name="id_kategori" id="id_kategori">
                                        <option selected>-- Pilih Kategori --</option>
                                        @foreach ($kategori as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $buku->id_kategori ? 'selected' : '' }}>
                                                {{ $item->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="pengarang">Pengarang</label>
                                    <input type="text" class="form-control" 
                                           id="pengarang" 
                                           name="pengarang" 
                                           placeholder="Isikan nama pengarang" 
                                           value="{{ $buku->pengarang }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="penerbit">Penerbit</label>
                                    <input type="text" class="form-control" 
                                           id="penerbit" 
                                           name="penerbit" 
                                           placeholder="Isikan nama penerbit" 
                                           value="{{ $buku->penerbit }}" required>
                                </div>
                            </div>

                            <!-- Kolom 2 -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="isbn">ISBN</label>
                                    <input type="text" class="form-control" 
                                           id="isbn" 
                                           name="isbn" 
                                           placeholder="Isikan ISBN" 
                                           value="{{ $buku->isbn }}" 
                                           onkeypress="return /[0-9, -]/i.test(event.key)" required>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="form-group">
                                            <label for="tahun_terbit">Tahun Terbit</label>
                                            <input type="text" class="form-control" 
                                                   id="tahun_terbit" 
                                                   name="tahun_terbit" 
                                                   placeholder="Isikan tahun terbit" 
                                                   value="{{ $buku->tahun_terbit }}" 
                                                   onkeypress="return /[0-9]/i.test(event.key)" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-6">
                                        <div class="form-group">
                                            <label for="stok">Stok</label>
                                            <input type="text" class="form-control" 
                                                   id="stok" 
                                                   name="stok" 
                                                   placeholder="Jumlah stok buku" 
                                                   value="{{ $buku->stok }}" 
                                                   onkeypress="return /[0-9]/i.test(event.key)" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="dipinjam">Dipinjam</label>
                                            <input type="text" class="form-control" 
                                                   id="dipinjam" 
                                                   name="dipinjam" 
                                                   placeholder="Buku dipinjam" 
                                                   value="{{ $buku->dipinjam }}" 
                                                   onkeypress="return /[0-9]/i.test(event.key)" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label for="dibooking">Dibooking</label>
                                            <input type="text" class="form-control" 
                                                   id="dibooking" 
                                                   name="dibooking" 
                                                   placeholder="Buku dibooking" 
                                                   value="{{ $buku->dibooking }}" 
                                                   onkeypress="return /[0-9]/i.test(event.key)" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom 3 (Gambar) -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="image">Gambar</label>
                                    <div class="custom-file">
                                        <input type="hidden" name="oldImage" value="{{ $buku->image }}" id="oldImage">
                                        <input type="file" class="custom-file-input" 
                                               id="image" name="image" 
                                               accept="image/x-png,image/jpg,image/jpeg" 
                                               onchange="previewImage()">
                                        <label class="custom-file-label" for="image">Pilih file...</label>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <img class="img-preview img-fluid mb-3 col-sm-5 d-block" 
                                         src="{{ asset('storage/' . $buku->image) }}" 
                                         style="width: 100%;">
                                    <a href="#" class="btn btn-info mt-2 d-none" id="reset" onclick="resetImage()">Reset Image</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan dan Kembali -->
                    <div class="card-footer">
                        <a href="{{ URL::previous() }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage() {
        const image = $('#image');
        const imgPreview = $('.img-preview');
        const file = image[0].files[0];
        const reader = new FileReader();

        reader.onload = function(event) {
            imgPreview.attr('src', event.target.result);
        }

        if (file) {
            reader.readAsDataURL(file);
            $('.custom-file-label').html(file.name);
            $('#reset').removeClass('d-none');
        }
    }

    function resetImage() {
        const imgPreview = $('.img-preview');
        const oldImage = $('#oldImage').val();
        imgPreview.attr('src', '{{ asset('storage/') }}/' + oldImage);
        $('.custom-file-label').html('Pilih file...');
        $('#reset').addClass('d-none');
    }
</script>
@endpush
