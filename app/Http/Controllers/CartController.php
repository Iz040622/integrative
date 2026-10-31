<?php
// Chin Jia Jun

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoomHoldService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class CartController extends Controller
{
    // Display the cart contents
    public function index()
    {
        $cart = session('cart', []);
        return view('booking.cart', compact('cart'));
    }

    // Add a room to the cart and place a hold
    public function store(Request $request, RoomHoldService $roomHoldService)
    {
        $validated = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'paxNo' => 'required|integer|min:1',
        ]);

        try {
            // Initialize cart if not present
            if (!session()->has('cart')) {
                session(['cart' => []]);
            }

            // Add to cart session
            $cart = session('cart');
            $cart[] = $validated;
            session([
                'cart' => $cart,
                'cart_expires_at' => now()->addMinutes(15),
            ]);

            try {
                // Get current user
                $request = Request::create('/api/customer', 'GET');
                $response = Route::dispatch($request);
                $customer = json_decode($response->getContent(), true);
                // Place a hold on the room
                $roomHoldService->holdRoom($customer['id'], (int) $validated['room_id'], $validated['startDate'], $validated['endDate']);
                return redirect('/cart')->with('success', 'Room added to cart successfully!');
            } catch (\Exception $e) {
                // If hold fails, remove the item from cart
                $filtered = array_filter($cart, fn($item) => $item !== $validated);
                session(['cart' => array_values($filtered)]);

                return back()->withErrors(['cartError' => 'Failed to hold the room. Please try again.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['cartError' => 'Something went wrong, unable to add to cart'])->withInput();
        }
    }

    // Remove a room from the cart and release the hold
    public function destroy(Request $request, RoomHoldService $roomHoldService)
    {
        $validated = $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $index = (int) $validated['index'];

        // Check if cart exists 
        if (!session()->has('cart')) {
            return back()->withErrors(['cartError' => 'Unable to remove item. Please try again.'])->withInput();
        }

        $cart = session('cart');

        // Check if index is valid
        if (!isset($cart[$index])) {
            return back()->withErrors(['cartError' => 'Unable to remove item. Please try again.'])->withInput();
        }

        $roomId = $cart[$index]['room_id'];

        // Remove item from cart
        unset($cart[$index]);
        if (empty($cart)) {
            session()->forget(['cart', 'cart_expires_at']);
        } else {
            session([
                'cart' => array_values($cart),
                'cart_expires_at' => now()->addMinutes(15),
            ]);
        }

        try {
            // Get current user
            $request = Request::create('/api/customer', 'GET');
            $response = Route::dispatch($request);
            $customer = json_decode($response->getContent(), true);
            // Release the hold on the room
            $roomHoldService->releaseHoldByRoomId($customer['id'], (int) $roomId);
        } catch (\Exception $e) {
            return back()->withErrors(['cartError' => 'Failed to release the room hold. Please contact support.'])->withInput();
        }

        return back()->with('success', 'Item removed from cart successfully.');
    }

    // API endpoint to get current cart items
    public function apiGetCartItems()
    {
        try {
            $cart = session('cart', []);

            return response()->json([
                'status' => 'success',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch cart items.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
