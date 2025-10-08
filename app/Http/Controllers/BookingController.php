<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Room prices
    private $prices = [
        'premium_deluxe' => 12000,
        'super_deluxe' => 10000,
        'standard_deluxe' => 8000,
    ];

    // Show booking form
    public function index()
    {
        return view('booking');
    }

    // Check availability and show rooms
    public function checkRooms(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => ['required', 'regex:/^(01|\+8801)[3-9]\d{8}$/'],
                'from_date' => 'required|date|after_or_equal:today',
                'to_date' => 'required|date|after:from_date',
            ]);
        } catch (\Exception $e) {
            return redirect('/')->withErrors($e->getMessage())->withInput();
        }

        $rooms = [];
        foreach ($this->prices as $type => $price) {
            $available = $this->isAvailable($type, $request->from_date, $request->to_date);
            $priceInfo = $this->calculatePrice($type, $request->from_date, $request->to_date);
            
            $rooms[$type] = [
                'name' => ucwords(str_replace('_', ' ', $type)),
                'available' => $available,
                'price' => $priceInfo
            ];
        }

        return view('select-room', [
            'data' => $request->all(),
            'rooms' => $rooms
        ]);
    }

    // Confirm booking
    public function confirm(Request $request)
    {
        try {
            $request->validate(['room_category' => 'required']);

            if (!$this->isAvailable($request->room_category, $request->from_date, $request->to_date)) {
                return redirect('/')->with('error', 'Room not available. Please search again.');
            }

            $priceInfo = $this->calculatePrice($request->room_category, $request->from_date, $request->to_date);

            $booking = Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'room_category' => $request->room_category,
                'total_nights' => $priceInfo['nights'],
                'base_price' => $priceInfo['base'],
                'weekend_surcharge' => $priceInfo['weekend_surcharge'],
                'discount' => $priceInfo['discount'],
                'final_price' => $priceInfo['final'],
            ]);

            return redirect('/thank-you/' . $booking->id);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Booking failed. Please try again.');
        }
    }

    // Thank you page
    public function thankYou($id)
    {
        $booking = Booking::findOrFail($id);
        return view('thank-you', compact('booking'));
    }

    // Check if room is available
    private function isAvailable($category, $from, $to)
    {
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);
        
        for ($date = $from->copy(); $date->lt($to); $date->addDay()) {
            $count = Booking::where('room_category', $category)
                ->where('from_date', '<=', $date)
                ->where('to_date', '>', $date)
                ->count();
            
            if ($count >= 3) return false;
        }
        
        return true;
    }

    // Calculate price with weekend and discount
    private function calculatePrice($category, $from, $to)
    {
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);
        $basePrice = $this->prices[$category];
        $baseTotal = 0;
        $weekendSurcharge = 0;
        $nights = 0;

        for ($date = $from->copy(); $date->lt($to); $date->addDay()) {
            $baseTotal += $basePrice;
            $nights++;
            
            // Add 20% on Friday(5) and Saturday(6)
            if (in_array($date->dayOfWeek, [5, 6])) {
                $weekendSurcharge += ($basePrice * 0.2);
            }
        }

        $subtotal = $baseTotal + $weekendSurcharge;
        $discount = 0;

        // 10% discount for 3+ nights
        if ($nights >= 3) {
            $discount = $subtotal * 0.1;
        }

        $final = $subtotal - $discount;

        return [
            'nights' => $nights,
            'base' => $baseTotal,
            'weekend_surcharge' => $weekendSurcharge,
            'discount' => $discount,
            'final' => $final
        ];
    }

    // Get disabled dates for calendar
    public function disabledDates()
    {
        $dates = [];
        $start = Carbon::today();
        $end = Carbon::today()->addMonths(3);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $fullyBooked = true;
            
            foreach (array_keys($this->prices) as $category) {
                if ($this->isAvailable($category, $date, $date->copy()->addDay())) {
                    $fullyBooked = false;
                    break;
                }
            }
            
            if ($fullyBooked) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return response()->json(['disabled' => $dates]);
    }
}