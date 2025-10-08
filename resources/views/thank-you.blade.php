<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 50px 0; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <div class="card">
            <div class="card-body text-center p-5">
                <div class="text-success mb-4" style="font-size: 80px;">✓</div>
                <h2 class="text-success mb-3">Booking Confirmed!</h2>
                <p class="lead">Thank you, {{ $booking->name }}!</p>

                <div class="card mt-4 text-start">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Booking Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Booking ID:</strong></td>
                                <td>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $booking->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $booking->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $booking->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Room:</strong></td>
                                <td>{{ ucwords(str_replace('_', ' ', $booking->room_category)) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Check-in:</strong></td>
                                <td>{{ $booking->from_date->format('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Check-out:</strong></td>
                                <td>{{ $booking->to_date->format('d M, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Nights:</strong></td>
                                <td>{{ $booking->total_nights }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td>{{ number_format($booking->base_price, 2) }} BDT</td>
                            </tr>
                            @if($booking->weekend_surcharge > 0)
                            <tr class="text-warning">
                                <td><strong>Weekend Surcharge (+20%):</strong></td>
                                <td>{{ number_format($booking->weekend_surcharge, 2) }} BDT</td>
                            </tr>
                            @endif
                            @if($booking->discount > 0)
                            <tr class="text-success">
                                <td><strong>Discount (3+ nights -10%):</strong></td>
                                <td>-{{ number_format($booking->discount, 2) }} BDT</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                            <tr>
                                <td><h5><strong>Final Price:</strong></h5></td>
                                <td><h5 class="text-success"><strong>{{ number_format($booking->final_price, 2) }} BDT</strong></h5></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-info mt-4 text-start">
                    <h6><strong>Next Steps:</strong></h6>
                    <ul class="mb-0">
                        <li>Confirmation email sent to {{ $booking->email }}</li>
                        <li>Check-in time: 2:00 PM</li>
                        <li>Check-out time: 11:00 AM</li>
                        <li>Bring valid ID proof</li>
                    </ul>
                </div>

                <a href="/" class="btn btn-primary w-100 mt-3">Make Another Booking</a>
            </div>
        </div>
    </div>
</body>
</html