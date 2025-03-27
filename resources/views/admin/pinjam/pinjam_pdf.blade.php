<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Pinjam</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Transaksi Pinjam</h1>
    <p>Periode: {{ request('start_date') }} - {{ request('end_date') }}</p>
    <table>
        <thead>
            <tr>
                <th>No Pinjam</th>
                <th>Tanggal Pinjam</th>
                <th>Judul Buku</th>
                <th>Status</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_pinjam as $pinjam)
                @foreach ($pinjam->pinjam_detail as $detail)
                    <tr>
                        <td>{{ $pinjam->no_pinjam }}</td>
                        <td>{{ \Carbon\Carbon::parse($pinjam->tgl_pinjam)->format('d-m-Y') }}</td>
                        <td>{{ $detail->buku->judul_buku ?? '' }}</td>
                        <td>{{ $detail->status }}</td>
                        <td>{{ $pinjam->petugas_pinjam->nama ?? 'Tidak Ada Petugas' }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>