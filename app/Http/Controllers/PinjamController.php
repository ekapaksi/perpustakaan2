<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pinjam;
use Carbon\Carbon;
use App\Models\Buku;
use App\Models\Booking;
use App\Models\PinjamDetail;
use App\Models\BookingDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PinjamController extends Controller
{
    public function index()
    {
        return view('admin.pinjam.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            if (empty($startDate) || empty($endDate)) {
                $data_pinjam = Pinjam::with([
                    'pinjam_detail' => function ($query) {
                        $query->where('status', 'Pinjam');
                    },
                    'pinjam_detail.buku',
                    'pinjam_detail.petugas_kembali',
                    'petugas_pinjam',
                    'anggota'
                ])
                    ->whereHas('pinjam_detail', function ($query) {
                        $query->where('status', 'Pinjam');
                    })
                    ->orderBy('no_pinjam', 'DESC')
                    ->get();
            } else {
                $endDate = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
                $data_pinjam = Pinjam::with([
                    'pinjam_detail' => function ($query) {
                        $query->where('status', 'Pinjam');
                    },
                    'pinjam_detail.buku',
                    'pinjam_detail.petugas_kembali',
                    'petugas_pinjam',
                    'anggota'
                ])
                    ->whereHas('pinjam_detail', function ($query) {
                        $query->where('status', 'Pinjam');
                    })
                    ->whereBetween('tgl_pinjam', [$startDate, $endDate])
                    ->orderBy('no_pinjam', 'DESC')
                    ->get();
            }

            $flattenedData = $data_pinjam->flatMap(function ($pinjam) {
                return $pinjam->pinjam_detail->map(function ($detail) use ($pinjam) {
                    return [
                        'no_pinjam' => $pinjam->no_pinjam,
                        'tgl_pinjam' => $pinjam->tgl_pinjam,
                        'tgl_kembali' => $detail->tgl_kembali,
                        'judul_buku' => $detail->buku->judul_buku ?? '',
                        'status' => $detail->status ?? '',
                        'gambar' => isset($detail->buku->image) ? asset('storage/' . $detail->buku->image) : '',
                        'anggota' => $pinjam->anggota->nama ?? 'Tidak Ada Anggota',
                        'petugas' => $pinjam->petugas_pinjam->nama ?? 'Tidak Ada Petugas',
                        'id_buku' => $detail->buku->id ?? '',
                    ];
                });
            });

            return DataTables::of($flattenedData)
                ->addIndexColumn()
                ->addColumn('tgl_pinjam', function ($row) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $row['tgl_pinjam'])->format('d-m-Y');
                })
                ->addColumn('tgl_kembali', function ($row) {
                    return $row['tgl_kembali'] ? Carbon::createFromFormat('Y-m-d H:i:s', $row['tgl_kembali'])->format('d-m-Y') : '';
                })
                ->addColumn('judul_buku', function ($row) {
                    return $row['judul_buku'];
                })
                ->addColumn('status', function ($row) {
                    return $row['status'];
                })
                ->addColumn('gambar', function ($row) {
                    if ($row['gambar']) {
                        return '<img src="' . $row['gambar'] . '" alt="Cover Buku" style="width: 100%;" class="img-pinjam">';
                    }
                    return '';
                })
                ->addColumn('anggota', function ($row) {
                    return $row['anggota'];
                })
                ->addColumn('petugas', function ($row) {
                    return $row['petugas'];
                })
                ->addColumn('aksi', function ($row) {
                    $url = url("admin/transaksi/pinjam/kembalikanBuku/{$row['no_pinjam']}/{$row['id_buku']}");
                    return '
                <form action="' . $url . '" method="POST">
                    ' . csrf_field() . '
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="btn btn-md btn-primary kembalikan-buku" data-toggle="tooltip" data-placement="top" title="Kembalikan Buku">
                        <i class="fas fa-angle-double-left"></i> Kembalikan
                    </button>
                </form>';
                })
                ->rawColumns([' gambar', 'aksi'])
                ->make(true);
        }
    }
    public function sedangPinjam(User $user)
    {
        // Mengambil data peminjaman yang belum dikembalikan (sedang pinjam)
        $sedang_pinjam = Pinjam::with(['pinjam_detail' => function ($query) {
            $query->where('status', 'Pinjam');
        }, 'pinjam_detail.buku', 'pinjam_detail.petugas_kembali', 'petugas_pinjam'])
            ->where('id_user', $user->id)
            ->orderBy('no_pinjam', 'ASC')
            ->get();

        if (count($sedang_pinjam) == 0) {
            return redirect()->route('member.index')->with('info', 'Tidak ada buku yang sedang dipinjam');
        }
        // echo json_encode($sedang_pinjam);exit;
        return view('member.sedang_pinjam', compact('sedang_pinjam'));
    }
    public function riwayatPinjam(User $user)
    {
        $riwayat_pinjam = Pinjam::with(
            'pinjam_detail',
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam'
        )->where('id_user', $user->id)
            ->orderBy('no_pinjam', 'ASC')
            ->get();
        if (count($riwayat_pinjam) == 0) {
            return redirect()->route('member.index')->with('info', 'Tidak ada riwayat peminjaman');
        }
        // echo json_encode($riwayat_pinjam);exit;
        return view('member.riwayat_pinjam', compact('riwayat_pinjam'));
    }
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $id_booking = $request->input('id_booking');
            $booking = Booking::where('id_booking', $id_booking)->first();
            $id_user = $booking->id_user;

            // Membuat no_pinjam dengan format Pyymmdd001
            $todayDate = Carbon::now()->format('ymd');
            $nextPinjamNumber = str_pad(Pinjam::whereDate('created_at', Carbon::today())->count() + 1, 3, '0', STR_PAD_LEFT);
            $no_pinjam = 'P' . $todayDate . $nextPinjamNumber;
            $tgl_pinjam = Carbon::now();
            $status = 'Pinjam';

            // Simpan data ke tabel pinjam
            $pinjam = Pinjam::create([
                'no_pinjam' => $no_pinjam,
                'tgl_pinjam' => $tgl_pinjam,
                'id_booking' => $id_booking,
                'id_user' => $id_user,
                'id_petugas_pinjam' => Auth::user()->id,
            ]);

            // Simpan data ke tabel pinjam_detail
            foreach ($request->input('denda') as $index => $denda) {
                $id_buku = BookingDetail::where('id_booking', $id_booking)->pluck('id_buku')[$index];
                $lama_pinjam = $request->input('lama')[$index];
                $tgl_kembali = Carbon::parse($tgl_pinjam)->addDays((int)$lama_pinjam);

                PinjamDetail::create([
                    'no_pinjam' => $no_pinjam,
                    'id_buku' => $id_buku,
                    'tgl_kembali' => $tgl_kembali,
                    'tgl_pengembalian' => null,
                    'denda' => $denda,
                    'lama_pinjam' => $lama_pinjam,
                    'status' => $status,
                ]);

                // Update stok buku
                $buku = Buku::find($id_buku);
                $buku->dibooking -= 1;
                $buku->dipinjam += 1;
                $buku->save();
            }

            // Hapus data dari tabel booking dan booking_detail
            BookingDetail::where('id_booking', $id_booking)->delete();
            Booking::where('id_booking', $id_booking)->delete();
        });

        return redirect()->route('admin.transaksi.booking.index')->with('success', 'Data pinjaman berhasil disimpan dan booking telah dihapus.');
    }
    public function exportPdfPinjam(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
    
        $data_pinjam = Pinjam::with([
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam'
        ])
        ->whereHas('pinjam_detail', function ($query) {
            $query->where('status', 'Pinjam');
        })
        ->whereBetween('tgl_pinjam', [$startDate, $endDate])
        ->orderBy('no_pinjam', 'DESC')
        ->get();
    
        $pdf = PDF::loadView('admin.pinjam.pinjam_pdf', compact('data_pinjam'));
        return $pdf->download('transaksi_pinjam.pdf');
    }
    public function kembalikanBuku($no_pinjam, $id_buku)
    {
        // Temukan detail pinjaman berdasarkan no_pinjam dan id_buku
        $pinjamDetail = PinjamDetail::where('no_pinjam', $no_pinjam)
            ->where('id_buku', $id_buku)
            ->first();
    
        // Jika detail pinjaman ditemukan, update status menjadi "Dikembalikan"
        if ($pinjamDetail) {
            $pinjamDetail->tgl_pengembalian = now();
            $pinjamDetail->status = 'Kembali';
            $pinjamDetail->id_petugas_kembali = Auth::user()->id;
            $pinjamDetail->save();
    
            return response()->json(['success' => 'Buku berhasil dikembalikan.']);
        }
    
        return response()->json(['error' => 'Detail pinjaman tidak ditemukan.'], 404);
    }
    
    public function pengembalian_index()
    {
        $data_pinjam = Pinjam::with([
            'pinjam_detail' => function ($query) {
                $query->where('status', 'Kembali');
            },
            'pinjam_detail.buku',
            'pinjam_detail.petugas_kembali',
            'petugas_pinjam'
        ])
        ->whereHas('pinjam_detail', function ($query) {
            $query->where('status', 'Kembali');
        })
        ->orderBy('no_pinjam', 'DESC')
        ->get();
    
        return view('admin.pinjam.pengembalian_index', compact('data_pinjam'));
    }
}
