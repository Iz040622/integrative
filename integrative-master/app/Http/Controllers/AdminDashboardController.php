<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\Crud\CrudRoom;
use App\Models\Crud\CrudRoomType;
use App\Models\RoomType;
use App\Models\PromoCode;
use App\Models\Crud\CrudPromoCode;
use App\Models\Admin;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Services\User\AdminActionFactory;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly AdminActionFactory $factory)
    {
        $this->middleware('auth');
        $this->middleware('admin')->only([
            'dashboard',
            'storeRoomType',
            'updateRoomType',
            'storeRoom',
            'updateRoom',
            'storePromo',
            'updatePromo'
        ]);
        $this->middleware('superadmin')->only(['storeAdmin', 'updateAdmin']);
    }
    private function loadDashboardData()
    {
        $monthlySales = DB::table('bookings')
            ->join('booking_room', 'bookings.id', '=', 'booking_room.booking_id')
            ->join('payment', 'bookings.payment_id', '=', 'payment.id')
            ->where('booking_room.status', 'Completed')
            ->selectRaw('YEAR(booking_room.checkInDate) as year, MONTH(booking_room.checkInDate) as month, SUM(payment.totalAmount) as total_sales')
            ->groupBy(DB::raw('YEAR(booking_room.checkInDate)'), DB::raw('MONTH(booking_room.checkInDate)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $roomTypeSales = DB::table('bookings')
            ->join('booking_room', 'bookings.id', '=', 'booking_room.booking_id')
            ->join('rooms', 'booking_room.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereYear('booking_room.checkInDate', '>=', now()->year - 1)
            ->where('booking_room.status', 'Completed')
            ->select(
                DB::raw('YEAR(booking_room.checkInDate) as year'),
                'room_types.name as room_type',
                DB::raw('MONTH(booking_room.checkInDate) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('YEAR(booking_room.checkInDate)'), 'room_types.name', DB::raw('MONTH(booking_room.checkInDate)'))
            ->get();

        return compact('monthlySales', 'roomTypeSales');
    }

    public function dashboard($activeSection = 'dashboard')
    {
        $data = $this->loadDashboardData();
        $data['promoCodes'] = PromoCode::orderByDesc('created_at')->get();
        $data['rooms'] = Room::with('roomType')->paginate(5);
        $data['roomTypes'] = RoomType::all();
        $data['admins'] = Admin::with('user')->get();
        $data['activeSection'] = $activeSection;


        return view('admin', $data);
    }

    public function storeRoom(Request $request)
    {
        $crud = new CrudRoom();
        try {
            $crud->process($request->all());
            return redirect('/admin#room-section')->with('success', 'Room created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#room-section')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect('/admin#room-section')
                ->with('error', 'Failed to create room: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateRoom(Request $request, $id)
    {
        $crud = new CrudRoom();
        try {
            $crud->processUpdate($request->all(), $id);
            return redirect('/admin#room-section')->with('success', 'Room updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#room-section')
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect('/admin#room-section')
                ->with('error', 'Failed to update room: ' . $e->getMessage());
        }
    }

    public function storeRoomType(Request $request)
    {
        $crud = new CrudRoomType();
        try {
            $crud->process($request->all());
            return redirect('/admin#room-section')->with('success', 'Room type created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
            return redirect('/admin#room-section')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            dd($e->getMessage());

            return redirect('/admin#room-section')
                ->with('error', 'Failed to create room type: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateRoomType(Request $request, $id)
    {
        $crud = new CrudRoomType();
        try {
            $crud->processUpdate($request->all(), $id);
            return redirect('/admin#room-section')->with('success', 'Room type updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#room-section')->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect('/admin#room-section')
                ->with('error', 'Failed to update room type: ' . $e->getMessage());
        }
    }

    public function storePromo(Request $request)
    {
        $crud = new CrudPromoCode();

        try {
            $crud->process($request->all());
            return redirect('/admin#promocode')->with('success', 'Promo Code created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#promocode')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect('/admin#promocode')
                ->with('error', 'Failed to create promo code: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updatePromo(Request $request, $id)
    {
        $crud = new CrudPromoCode();

        try {
            $crud->processUpdate($request->all(), $id);
            return redirect('/admin#promocode')->with('success', 'Promo Code updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#promocode')
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect('/admin#promocode')
                ->with('error', 'Failed to update promo code: ' . $e->getMessage());
        }
    }

    public function storeAdmin(Request $request)
    {
        try {
            $this->factory->make('admin.create')->execute($request->all());
            return redirect('/admin#admin-section')->with('success', 'Admin created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#admin-section')->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect('/admin#admin-section')->with('error', 'Failed to create admin: ' . $e->getMessage())->withInput();
        }
    }

    public function updateAdmin(Request $request, $id)
    {
        try {
            $this->factory->make('admin.update')->execute($request->all(), $id);
            return redirect('/admin#admin-section')->with('success', 'Admin updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect('/admin#admin-section')->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect('/admin#admin-section')->with('error', 'Failed to update admin: ' . $e->getMessage());
        }
    }
}
