@extends('admin.layout.main')
@section('title', 'Transaksi Booking')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table￾striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID Booking</th>
                                    <th>Tanggal Booking</th>
                                    <th>Batas Ambil</th>
                                    <th>Anggota</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->id_booking }}</td>
                                        <td>{{ $item->tgl_booking }}</td>
                                        <td>{{ $item->batas_ambil }}</td>
                                        <td>{{ $item->anggota->nama }}</td>
                                        <td>
                                            <form action="{{ route('admin.transaksi.booking.destroy', $item->id) }}"
                                                method="POST">
                                                <a href="{{ route('admin.transaksi.booking.show', $item->id) }}"
                                                    class="btn btn-xs btn-primary" data-toggle="tooltip"
                                                    data-placement="top" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-xs btn-danger hapus-data"
                                                    data-toggle="tooltip" data-placement="top" title="Hapus"><I
                                                        class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

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
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
        });
        $('[data-toggle="tooltip"]').tooltip();
        $(document).on('click', '.hapus-data', function() {
            var form = $(this).closest("form");
            if (confirm('Yakin ingin menghapus data??')) {
                form.submit();
            }
        });
    </script>
@endpush
