<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Data Booking</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #cccccc;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Data Booking</h1>
    <table>
        <tr>
            <th>ID Booking</th>
            <th>Tanggal Booking</th>
            <th>Batas Ambil</th>
        </tr>
        <tr>
            <td>{{ $data_booking[0]->id_booking }}</td>
            <td>{{ $data_booking[0]->tgl_booking }}</td>
            <td>{{ $data_booking[0]->batas_ambil }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th class="center">#</th>
                <th class="center">ID Booking</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_booking as $booking)
                @foreach ($booking->booking_detail as $detail)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $booking->id_booking }}</td>
                        <td>{{ $detail->buku->judul_buku }}</td>
                        <td>{{ $detail->buku->pengarang }}</td>
                        <td>{{ $detail->buku->penerbit }}</td>
                        <td>{{ $detail->buku->tahun_terbit }}</td>
                        <td>{{ $detail->buku->kategori->nama_kategori }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>

</html>
