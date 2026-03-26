# Hotel Booking Search Engine

A production-ready Laravel-based hotel booking search engine with pricing, availability, and discount calculation.

## Features

- Room availability search
- Dynamic pricing based on occupancy and dates
- Multiple meal plan support
- Configurable discount system (Last Minute, Long Stay)
- Clean REST API
- Comprehensive test coverage

## Requirements

- PHP 8.2+
- Composer
- SQLite (default for development) or MySQL/PostgreSQL

## Installation

1. **Clone and install dependencies:**
   ```bash
   cd hotel-search
   composer install
   ```

2. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

3. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

4. **Run migrations and seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Start the development server:**
   ```bash
   php artisan serve
   ```

   The API will be available at `http://localhost:8000`

## Testing the API

### Search Endpoint

**Endpoint:** `GET /api/search`

**Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| check_in | string (Y-m-d) | Yes | Check-in date |
| check_out | string (Y-m-d) | Yes | Check-out date |
| adults | integer (1-3) | Yes | Number of adults |
| meal_plan | string | Yes | `room_only` or `breakfast` |

**Example Request:**
```bash
curl -H "Accept: application/json" \
  "http://localhost:8000/api/search?check_in=2026-04-01&check_out=2026-04-05&adults=2&meal_plan=breakfast"
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "room_type_id": 1,
      "room_type": "Standard Room",
      "room_type_slug": "standard",
      "available": true,
      "available_rooms": 3,
      "meal_plan": "Bed & Breakfast",
      "price_breakdown": {
        "base_price": 558.63,
        "meal_price": 280,
        "subtotal": 838.63,
        "discount": 125.79,
        "final_price": 712.84,
        "applied_discounts": [
          {
            "name": "Long Stay Discount",
            "slug": "long_stay",
            "type": "percentage",
            "value": 15,
            "amount": 125.79
          }
        ],
        "nights": 4,
        "price_per_night": 178.21
      }
    }
  ],
  "meta": {
    "check_in": "2026-04-01",
    "check_out": "2026-04-05",
    "adults": 2,
    "meal_plan": "breakfast",
    "nights": 4,
    "total_results": 2,
    "available_count": 2
  }
}
```

### Health Check

```bash
curl http://localhost:8000/api/health
```

## Configuration

### Discount Configuration

Edit `config/discounts.php` to modify discount rules:

```php
'last_minute' => [
    'enabled' => true,
    'min_days_before_checkin' => 0,
    'max_days_before_checkin' => 3,
    'discount_type' => 'percentage',
    'discount_value' => 10,
    'stackable' => true,
],

'long_stay' => [
    'enabled' => true,
    'min_nights' => 3,
    'discount_type' => 'percentage',
    'discount_value' => 15,
    'stackable' => true,
],
```

## Running Tests

```bash
php artisan test
```

## Project Structure

```
app/
├── DTO/                    # Data Transfer Objects
│   ├── SearchRequestDTO.php
│   ├── SearchResultDTO.php
│   ├── PriceBreakdownDTO.php
│   └── DiscountDTO.php
├── Enums/
│   └── MealPlanType.php
├── Http/Controllers/Api/
│   └── SearchController.php
├── Interfaces/             # Service contracts
│   ├── SearchServiceInterface.php
│   ├── PricingServiceInterface.php
│   └── DiscountServiceInterface.php
├── Models/                 # Eloquent models
│   ├── Property.php
│   ├── RoomType.php
│   ├── Room.php
│   ├── Inventory.php
│   ├── PricingRule.php
│   ├── MealPlan.php
│   ├── DiscountType.php
│   └── DiscountRule.php
└── Services/               # Business logic
    ├── SearchService.php
    ├── PricingService.php
    └── DiscountService.php
```

## License

MIT
