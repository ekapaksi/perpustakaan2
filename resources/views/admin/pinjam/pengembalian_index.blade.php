@extends('admin.layout.main')
@section('title', 'Transaksi Pengembalian')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped" style="font-size: small">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Pinjam</th>
                                <th>Tgl. Pinjam</th>
                                <th>Tgl. Kembali</th>
                                <th>Lama Pinjam</th>
                                <th>Tgl. Pengembalian</th>
                                <th>Judul Buku</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Gambar</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_pinjam as $pinjam)
                                @foreach ($pinjam->pinjam_detail as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pinjam->no_pinjam }}</td>
                                        <td>{{ date('d-m-Y', strtotime($pinjam->tgl_pinjam)) }}</td>
                                        <td>{{ date('d-m-Y', strtotime($detail->tgl_kembali)) }}</td>
                                        <td>{{ $detail->lama_pinjam }} hari</td>
                                        <td>
                                            {{ $detail->tgl_pengembalian == null ? '-' : date('d-m-Y', strtotime($detail->tgl_pengembalian)) }}
                                        </td>
                                        <td>{{ $detail->buku->judul_buku }}</td>
                                        <td>
                                            @if ($detail->status == 'Pinjam')
                                                <span class="badge badge-info">{{ $detail->status }}</span>
                                            @else
                                                <span class="badge badge-success">{{ $detail->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <p>
                                                <b>Denda: </b><br>
                                                Rp {{ number_format($detail->denda, 0, ',', '.') }}
                                            </p>
                                            <p>
                                                <b>Terlambat: </b><br>
                                                @php
                                                    $tglKembali = \Carbon\Carbon::parse($detail->tgl_kembali);
                                                    $terlambat = '-';
                                                    if (!is_null($detail->tgl_pengembalian)) {
                                                        $tglPengembalian = \Carbon\Carbon::parse($detail->tgl_pengembalian);
                                                        if ($tglPengembalian->gt($tglKembali)) {
                                                            $terlambat = round($tglKembali->diffInDays($tglPengembalian));
                                                        } else {
                                                            $terlambat = 0; // Tidak terlambat
                                                        }
                                                    }
                                                @endphp
                                                {{ round($terlambat) }} hari
                                            </p>
                                            <p>
                                                <b>Total Denda: </b><br>
                                                @php
                                                    if ($terlambat == '-') {
                                                        $totalDenda = '-';
                                                    } else {
                                                        $totalDenda = round($detail->denda * $terlambat);
                                                    }
                                                @endphp
                                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <img src="{{ asset('storage/' . $detail->buku->image) }}" alt="Cover Buku" style="width: 50%;" class="img-pinjam">
                                        </td>
                                        <td>
                                            <p>
                                                <b>Pinjam: </b><br>
                                                {{ $pinjam->petugas_pinjam->nama }}
                                            </p>
                                            <p>
                                                <b>Kembali: </b><br>
                                                {{ $detail->petugas_kembali != null ? $detail->petugas_kembali->nama : '-' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
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

    $('[data-toggle="tooltip"]').tooltip();

    $(document).on('click', '.kembalikan-buku', function() {
        var form = $(this).closest("form");
        if (confirm('Yakin ingin mengembalikan buku ini?')) {
            form.submit();
        }
    });
});
</script>
@endpush