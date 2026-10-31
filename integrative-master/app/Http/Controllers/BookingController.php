<?php
// Chin Jia Jun

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BookingController extends Controller
{
    //Retrieve and display a list of bookings for the authenticated user
    public function index()
    {
        try {
            $request = Request::create('/api/customer', 'GET');
            $response = Route::dispatch($request);
            $customer = json_decode($response->getContent(), true);
            $bookings = Booking::where('user_id', $customer['id'])->latest()->get();

            return view('booking.list', compact('bookings'));
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to load bookings. Please try again later.');
        }
    }

    // Show the form for creating a new booking
    public function create()
    {
        return view('booking.create');
    }

    // API endpoint to get all bookings (for admin use)
    public function apiGetBookings()
    {
        try {
            $bookings = Booking::all();
            return response()->json([
                'status' => 'success',
                'bookings' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }
}
