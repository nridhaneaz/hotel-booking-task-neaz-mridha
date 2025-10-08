<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body { background: #f5f5f5; padding: 50px 0; }
        .container { max-width: 600px; }
        .card { box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">🏨 Hotel Booking</h3>
            </div>
            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="/check-rooms">
                    @csrf
                    <div class="mb-3">
                        <label>Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Phone *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" 
                               placeholder="01712345678" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Check-in *</label>
                            <input type="text" name="from_date" id="from_date" class="form-control" required readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Check-out *</label>
                            <input type="text" name="to_date" id="to_date" class="form-control" required readonly>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let disabled = [];
        fetch('/disabled-dates')
            .then(r => r.json())
            .then(data => {
                disabled = data.disabled;
                initDates();
            });

        function initDates() {
            const from = flatpickr("#from_date", {
                minDate: "today",
                dateFormat: "Y-m-d",
                disable: disabled,
                onChange: function(selectedDates) {
                    to.set('minDate', new Date(selectedDates[0].getTime() + 86400000));
                }
            });

            const to = flatpickr("#to_date", {
                minDate: "today",
                dateFormat: "Y-m-d",
                disable: disabled
            });
        }
    </script>
</body>
</html>
