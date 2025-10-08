# 🏨 Hotel Booking System

A complete Laravel-based hotel booking system with dynamic pricing, availability management, and discount features.

## ✨ Features

- **3 Room Categories**: Premium Deluxe, Super Deluxe, Standard Deluxe
- **Dynamic Pricing**: Base prices with weekend surcharges (20% on Fri & Sat)
- **Smart Discounts**: Automatic 10% discount for stays of 3+ nights
- **Availability Management**: 3 rooms per category with real-time availability checking
- **Date Validation**: Prevents past date bookings and shows disabled dates
- **Complete Booking Flow**: From search to confirmation with detailed price breakdown
- **Form Validation**: Email and Bangladesh phone number validation
- **Responsive Design**: Mobile-friendly Bootstrap interface

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Laravel 11.x

## 🚀 Installation

### Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel hotel-booking
cd hotel-booking
```

### Step 2: Create Database

Open your MySQL client and run:

```sql
CREATE DATABASE hotel_booking;
```

### Step 3: Configure Environment

Edit your `.env` file:

```env
APP_NAME="Hotel Booking"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_booking
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Create Files

#### 4.1 Migration File

Create `2025_10_08_050557_create_bookings_table.php`:

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('room_category');
            $table->integer('total_nights');
            $table->decimal('base_price', 10, 2);
            $table->decimal('weekend_surcharge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
```

Create `2025_10_08_123419_add_discount_to_bookings_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('weekend_surcharge', 10, 2)->default(0)->after('base_price');
        $table->decimal('discount', 10, 2)->default(0)->after('weekend_surcharge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};

```

#### 4.2 Model File

Create `app/Models/Booking.php`:

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];
    protected $casts = ['from_date' => 'date', 'to_date' => 'date'];
}
```

#### 4.3 Controller File

Create `app/Http/Controllers/BookingController.php` with the full controller code from the provided document.

#### 4.4 Routes File

Add to `routes/web.php`:

```php
<?php
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index']);
Route::post('/check-rooms', [BookingController::class, 'checkRooms']);
Route::post('/confirm', [BookingController::class, 'confirm']);
Route::get('/thank-you/{id}', [BookingController::class, 'thankYou']);
Route::get('/disabled-dates', [BookingController::class, 'disabledDates']);
```

#### 4.5 View Files

Create three blade files in `resources/views/`:
- `booking.blade.php` - Main booking form
- `select-room.blade.php` - Room selection page
- `thank-you.blade.php` - Confirmation page

Copy the content from the provided document.

### Step 5: Run Migration

```bash
php artisan migrate
```

### Step 6: Start Development Server

```bash
php artisan serve
```

### Step 7: Access Application

Open your browser and visit:

```
http://localhost:8000
```

## 💰 Pricing Structure

| Room Category      | Base Price (BDT) |
|-------------------|------------------|
| Premium Deluxe    | 12,000          |
| Super Deluxe      | 10,000          |
| Standard Deluxe   | 8,000           |

### Pricing Rules

1. **Weekend Surcharge**: +20% on Friday and Saturday nights
2. **Multi-night Discount**: -10% for stays of 3 or more nights
3. **Calculation Order**: Base Price → Weekend Surcharge → Discount

### Example Calculation

**Scenario**: Premium Deluxe room, Friday to Monday (3 nights)

```
Night 1 (Friday):   12,000 + 2,400 (20%) = 14,400 BDT
Night 2 (Saturday): 12,000 + 2,400 (20%) = 14,400 BDT
Night 3 (Sunday):   12,000             = 12,000 BDT
                                        ________
Subtotal:                               40,800 BDT
Discount (10%):                         -4,080 BDT
                                        ________
Final Price:                            36,720 BDT
```

## 🧪 Testing

### Test Case 1: Weekend Booking with Discount

1. Select check-in: Friday
2. Select check-out: Monday (4 nights)
3. Expected: Weekend surcharge + 10% discount applied

### Test Case 2: Room Availability Limit

1. Book the same room category 3 times for overlapping dates
2. Try to book a 4th time
3. Expected: Room shown as unavailable

### Test Case 3: Phone Validation

Try these phone numbers:
- ✅ Valid: `01712345678`, `+8801712345678`
- ❌ Invalid: `123456789`, `02-123456`

## 📁 Project Structure

```
hotel-booking/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── BookingController.php
│   └── Models/
│       └── Booking.php
├── database/
│   └── migrations/
│       └── 2025_10_08_050557_create_bookings_table
|         ├──2025_10_08_123419_add_discount_to_bookings_table
├── resources/
│   └── views/
│       ├── booking.blade.php
│       ├── select-room.blade.php
│       └── thank-you.blade.php
├── routes/
│   └── web.php
└── .env
```

## 🔧 Configuration

### Room Capacity

Each room category has 3 rooms available. To change this, modify the `isAvailable()` method in `BookingController.php`:

```php
if ($count >= 3) return false; // Change 3 to your desired capacity
```

### Room Prices

Update prices in the `BookingController.php`:

```php
private $prices = [
    'premium_deluxe' => 12000,  // Change these values
    'super_deluxe' => 10000,
    'standard_deluxe' => 8000,
];
```

### Discount Rules

Modify discount logic in the `calculatePrice()` method:

```php
if ($nights >= 3) {  // Change minimum nights
    $discount = $subtotal * 0.1;  // Change discount percentage
}
```

## 🐛 Troubleshooting

### Database Connection Error

```bash
# Clear config cache
php artisan config:clear

# Check database credentials in .env
```

### Migration Errors

```bash
# Rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

### Assets Not Loading

```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 📝 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Booking form |
| POST | `/check-rooms` | Check availability |
| POST | `/confirm` | Confirm booking |
| GET | `/thank-you/{id}` | Confirmation page |
| GET | `/disabled-dates` | Get unavailable dates (JSON) |

## 🔐 Validation Rules

- **Name**: Required
- **Email**: Required, valid email format
- **Phone**: Required, Bangladesh format (`01XXXXXXXXX` or `+8801XXXXXXXXX`)
- **Check-in**: Required, date, today or future
- **Check-out**: Required, date, after check-in
- **Room Category**: Required on confirmation

## 🎨 Customization

### Changing Colors

Edit the view files and modify Bootstrap classes:
- `bg-primary` → `bg-success`, `bg-dark`, etc.
- `text-success` → `text-primary`, `text-info`, etc.

### Adding New Room Categories

1. Add to `$prices` array in controller
2. System automatically handles the new category
3. No view changes needed

---

**Made with ❤️ using Laravel**
