<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\BookingTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all()->map(function ($category) {
            $category->icon = url('storage/' . $category->icon);
            return $category;
        });

        $tickets = Ticket::with('seller')->get()->map(function ($ticket) {
            $ticket->thumbnail = url('storage/' . $ticket->thumbnail);
            $ticket->location = $ticket->seller->location;
            unset($ticket->seller);
            return $ticket;
        });

        $popularTickets = Ticket::where('is_popular', true)->with('seller')->get()->map(function ($popularTicket) {
            $popularTicket->thumbnail = url('storage/' . $popularTicket->thumbnail);
            $popularTicket->location = $popularTicket->seller->location;
            unset($popularTicket->seller);
            return $popularTicket;
        });

        return response()->json([
            'message' => 'Home Data',
            'data' => [
                'categories' => $categories,
                'tickets' => $tickets,
                'popular_tickets' => $popularTickets
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:tickets,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'total_participant' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'phone_number' => 'required|string',
            'started_at' => 'required|date',
            'proof' => 'required|image',
        ]);


        $validated = $validator->validated();

        $validated['booking_trx_id'] = 'TRX-' . strtoupper(uniqid());

        $validated['is_paid'] = false;

        if ($request->hasFile('proof')) {
            $validated['proof'] = $request->file('proof')->store('booking_transactions/proof', 'public');
        }

        $booking = BookingTransaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking transaction created successfully',
            'data' => $booking,
        ]);
    }


    public function show($id)
    {
        $ticket = Ticket::with('category', 'seller', 'photos')->find($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->thumbnail = url('storage/' . $ticket->thumbnail);

        if ($ticket->seller) {
            $ticket->seller->photo = url('storage/' . $ticket->seller->photo);
        }

        if ($ticket->category) {
            $ticket->category->icon = url('storage/' . $ticket->category->icon);
        }

        $ticket->photos->transform(function ($photo) {
            $photo->photo = url('storage/' . $photo->photo);
            return $photo;
        });



        return response()->json([
            'message' => 'Ticket Detail',
            'data' => $ticket
        ], 200);
    }

    public function getBookingDetail($bookingTrxId, $phoneNumber)
    {
        // Cari booking transaction
        $booking = BookingTransaction::with(['ticket.category', 'ticket.seller'])
            ->where('booking_trx_id', $bookingTrxId)
            ->where('phone_number', $phoneNumber)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => true,
                'message' => 'Booking not found',
            ], 200);
        }

        if ($booking->proof) {
            $booking->proof = url('storage/' . $booking->proof);
        }

        if ($booking->ticket) {
            $booking->ticket->thumbnail = url('storage/' . $booking->ticket->thumbnail);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking details retrieved successfully',
            'data' => $booking,
        ]);
    }
}
