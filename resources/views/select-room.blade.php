<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 50px 0; }
        .room-card { cursor: pointer; transition: 0.3s; margin-bottom: 20px; }
        .room-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        .room-card.disabled { opacity: 0.5; cursor: not-allowed; }
        .room-card.disabled:hover { transform: none; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <div class="card mb-3">
            <div class="card-body">
                <strong>{{ $data['name'] }}</strong> | {{ $data['email'] }} | {{ $data['phone'] }}<br>
                <strong>Check-in:</strong> {{ $data['from_date'] }} | <strong>Check-out:</strong> {{ $data['to_date'] }}
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h3 class="mb-4">Select Your Room</h3>

        <form method="POST" action="/confirm">
            @csrf
            <input type="hidden" name="name" value="{{ $data['name'] }}">
            <input type="hidden" name="email" value="{{ $data['email'] }}">
            <input type="hidden" name="phone" value="{{ $data['phone'] }}">
            <input type="hidden" name="from_date" value="{{ $data['from_date'] }}">
            <input type="hidden" name="to_date" value="{{ $data['to_date'] }}">

            @foreach($rooms as $type => $room)
                <div class="card room-card {{ !$room['available'] ? 'disabled' : '' }}" 
                     onclick="{{ $room['available'] ? "document.getElementById('room_$type').checked=true" : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5>{{ $room['name'] }}</h5>
                                @if($room['available'])
                                    <p class="mb-1"><strong>Total Nights:</strong> {{ $room['price']['nights'] }}</p>
                                    <p class="mb-1"><strong>Base Price:</strong> {{ number_format($room['price']['base'], 2) }} BDT</p>
                                    @if($room['price']['weekend_surcharge'] > 0)
                                        <p class="mb-1 text-warning"><strong>Weekend Surcharge (+20%):</strong> {{ number_format($room['price']['weekend_surcharge'], 2) }} BDT</p>
                                    @endif
                                    @if($room['price']['discount'] > 0)
                                        <p class="mb-1 text-success"><strong>Discount (3+ nights -10%):</strong> -{{ number_format($room['price']['discount'], 2) }} BDT</p>
                                    @endif
                                    <hr>
                                    <h4 class="text-success mt-2">Total: {{ number_format($room['price']['final'], 2) }} BDT</h4>
                                @else
                                    <span class="badge bg-danger">No Room Available</span>
                                @endif
                            </div>
                            <div>
                                @if($room['available'])
                                    <input type="radio" name="room_category" value="{{ $type }}" id="room_{{ $type }}" required>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <button type="submit" class="btn btn-success w-100 mb-2">Confirm Booking</button>
            <a href="/" class="btn btn-outline-secondary w-100">Back</a>
        </form>
    </div>
</body>
</html>