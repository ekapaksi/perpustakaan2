@extends('admin.layout.main')
@section('title', 'Transaksi Peminjaman')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-2">
                            <input type="date" id="start_date" class="form-control" placeholder="Tanggal Mulai">
                        </div>
                        <div class="col-lg-2">
                            <input type="date" id="end_date" class="form-control" placeholder="Tanggal Akhir">
                        </div>
                        <div class="col-lg-8">
                            <button type="button" id="filter" class="btn btn-primary">
                                <i class="fas fa-solid fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-default" id="export-pdf">
                                <i class="far fa-file nav-icon"></i> Export PDF
                            </button>
                            <button type="button" class="btn btn-success">
                                <i class="fas fa-file nav-icon"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="transaksi-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Pinjam</th>
                                <th>Tgl. Pinjam</th>
                                <th>Tgl. Kembali</th>
                                <th>Judul Buku</th>
                                <th>Status</th>
                                <th>Gambar</th>
                                <th>Anggota</th>
                                <th>Petugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div><!-- /.container-fluid -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#transaksi-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.transaksi.peminjaman.data') }}",
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'no_pinjam', name: 'no_pinjam' },
            { data: 'tgl_pinjam', name: 'tgl_pinjam' },
            { data: 'tgl_kembali', name: 'tgl_kembali' },
            { data: 'judul_buku', name: 'judul_buku' },
            { data: 'status', name: 'status' },
            { data: 'gambar', name: 'gambar' },
            { data: 'anggota', name: 'anggota' },
            { data: 'petugas', name: 'petugas' },
            { data: 'aksi', name: 'aksi' }
        ]
    });

    // Filter data berdasarkan tanggal
    $('#filter').click(function() {
        table.draw();
    });

    // Initialize tooltips after DataTables draw event
    table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Handle click event on "Kembalikan Buku" buttons
    $(document).on('click', '.kembalikan-buku', function(event) {
        event.preventDefault(); // Prevent the default form submission
        var form = $(this).closest("form");
        var url = form.attr('action');
        var token = form.find('input[name="_token"]').val();
        var method = form.find('input[name="_method"]').val();

        if (confirm('Yakin ingin mengembalikan buku ini?')) {
            $.ajax({
                url: url,
                method: method,
                data: {
                    _token: token,
                    _method: method
                },
                success: function(response) {
                    // Reload DataTables
                    table.ajax.reload(null, false); // false to keep the current page
                    toastr.success('Buku berhasil dikembalikan!');
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan saat mengembalikan buku.');
                }
            });
        }
    });

    // Handle export PDF button click
    $('#export-pdf').on('click', function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        
        if (startDate === '' || endDate === '') {
            alert("Silakan pilih tanggal awal dan tanggal akhir.");
            return false;
        }

        var url = '{{ route('admin.transaksi.pinjam.exportPdfPinjam') }}?start_date=' + startDate + '&end_date=' + endDate;
        window.location.href = url;
    });
});
</script>
@endpush