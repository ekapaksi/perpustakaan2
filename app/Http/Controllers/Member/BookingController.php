<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function index()
    {
        $booking = Booking::with('anggota')->get();
        return view('admin.booking.index', compact('booking'));
    }
    public function dataBooking(User $user)
    {
        $data_booking = Booking::with('booking_detail', 'booking_detail.buku')->where('id_user', $user->id)->get();
        if (count($data_booking) == 0) {
            return redirect()->route('member.index')->with('info', 'Tidak ada buku 
    yang dibooking');
        }
        return view('member.data_booking', compact('data_booking'));
    }
    public function bookingPdf(User $user)
    {
        $data_booking = Booking::with('booking_detail')->get();
        // var_dump(print_r($data_booking,TRUE));
        $pdf = PDF::loadView('member.booking_pdf', compact('data_booking'));
        return $pdf->stream('data_booking.pdf');
    }
    public function show(string $id)
    {
        $data_booking = Booking::with(
            'booking_detail',
            'booking_detail.buku',
            'anggota'
        )->where('id', $id)->get();
        return view('admin.booking.show', compact('data_booking'));
    }
}
