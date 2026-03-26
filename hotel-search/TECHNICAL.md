# Technical Documentation

## Architecture Overview

This Hotel Booking Search Engine follows **Clean Architecture** principles with a clear separation of concerns:

```
┌─────────────────────────────────────────┐
│         Controllers (API Layer)          │
│         SearchController                  │
└─────────────────────┬─────────────────────┘
                      │
┌─────────────────────▼─────────────────────┐
│         Services (Business Logic)         │
│  SearchService │ PricingService │         │
│  DiscountService                            │
└─────────────────────┬─────────────────────┘
                      │
┌─────────────────────▼─────────────────────┐
│         Models (Data Layer)               │
│  RoomType │ Inventory │ PricingRule │     │
│  MealPlan │ DiscountType                    │
└───────────────────────────────────────────┘
```

## Database Schema

### Properties Table
- Stores hotel/property information
- Scalable for multiple properties

### Room Types Table
- Links to properties
- Contains room type metadata (max occupancy, total rooms)
- Normalized amenities stored as JSON

### Rooms Table
- Individual room instances
- Links to room types for detailed inventory tracking

### Inventories Table
- Daily availability tracking per room type
- Tracks: total rooms, booked rooms, blocked rooms
- Supports inventory closure (is_closed flag)

### Pricing Rules Table
- Base prices per room type, date, and occupancy
- Supports dynamic pricing based on:
  - Day of week (weekend pricing)
  - Seasonal variations
  - Occupancy tiers (1, 2, 3 adults)

### Meal Plans Table
- Stores meal plan configurations
- Price per person per night

### Rate Plans Table
- Links room types with meal plans
- Enables different rate combinations

### Discount Types Table
- Defines discount categories
- Supports percentage or fixed amount discounts
- Stackable flag for combining discounts

### Discount Rules Table
- Configures when discounts apply:
  - `valid_from` / `valid_to`: Date range validity
  - `min_nights` / `max_nights`: Stay length requirements
  - `min_days_before_checkin` / `max_days_before_checkin`: Booking timing
  - `room_type_id`: Property-wide or room-specific

### Bookings Table
- Simulates inventory blocking
- Status tracking (confirmed, cancelled, completed)

## Pricing Logic

### Base Price Calculation

1. **Fetch daily pricing rules** for the date range and occupancy
2. **Sum all daily prices** for the total base price
3. **Dynamic pricing factors**:
   - Weekend multiplier (1.2x)
   - Seasonal adjustment (0.9x - 1.3x)
   - Random variation (±10%)

### Meal Plan Pricing

```
meal_price = price_per_person_per_night × adults × nights
```

Example for Breakfast:
- $35/person/night × 2 adults × 4 nights = $280

### Final Price Formula

```
subtotal = base_price + meal_price
discount = sum of applicable discounts
final_price = subtotal - discount
price_per_night = final_price / nights
```

## Discount Logic

### Last Minute Discount
- **Trigger**: Check-in within 3 days of booking
- **Value**: 10% off
- **Stackable**: Yes (can combine with other discounts)

### Long Stay Discount
- **Trigger**: Stay of 3 or more nights
- **Value**: 15% off
- **Stackable**: Yes

### Discount Stacking

When multiple discounts apply:
1. Calculate each discount amount
2. Sum all discounts (if stackable)
3. Apply total discount to subtotal

## Service Layer

### SearchService
**Responsibilities:**
- Validate search parameters
- Fetch room types with availability
- Check inventory for entire date range
- Coordinate pricing calculation
- Return sorted results

**Key Methods:**
```php
public function search(SearchRequestDTO $request): Collection
public function validateRequest(SearchRequestDTO $request): void
```

### PricingService
**Responsibilities:**
- Calculate base price from daily rates
- Add meal plan costs
- Apply discounts
- Generate price breakdown

**Key Methods:**
```php
public function calculatePrice(
    SearchRequestDTO $request,
    int $roomTypeId,
    array $dailyPrices
): PriceBreakdownDTO

public function calculateMealPlanPrice(
    SearchRequestDTO $request,
    float $pricePerPersonPerNight
): float
```

### DiscountService
**Responsibilities:**
- Find applicable discounts
- Calculate discount amounts
- Handle stacking logic

**Key Methods:**
```php
public function calculateDiscounts(
    SearchRequestDTO $request,
    float $subtotal,
    int $roomTypeId
): array

public function getApplicableDiscounts(
    SearchRequestDTO $request,
    int $roomTypeId
): Collection
```

## Data Transfer Objects (DTOs)

### SearchRequestDTO
```php
public function __construct(
    public readonly Carbon $checkInDate,
    public readonly Carbon $checkOutDate,
    public readonly int $adults,
    public readonly MealPlanType $mealPlan,
)
```

### SearchResultDTO
```php
public function __construct(
    public readonly int $roomTypeId,
    public readonly string $roomTypeName,
    public readonly string $roomTypeSlug,
    public readonly bool $available,
    public readonly ?PriceBreakdownDTO $priceBreakdown,
    public readonly int $availableRooms,
    public readonly string $mealPlan,
)
```

### PriceBreakdownDTO
```php
public function __construct(
    public readonly float $basePrice,
    public readonly float $mealPrice,
    public readonly float $subtotal,
    public readonly float $discount,
    public readonly float $finalPrice,
    public readonly array $appliedDiscounts,
    public readonly int $nights,
    public readonly float $pricePerNight,
)
```

## API Design

### Request Validation

| Field | Type | Rules |
|-------|------|-------|
| check_in | string | Required, Y-m-d format |
| check_out | string | Required, Y-m-d, after check_in |
| adults | integer | Required, min:1, max:3 |
| meal_plan | enum | Required, room_only or breakfast |

### Error Handling

**Validation Error (422):**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Adults must be between 1 and 3"
  }
}
```

**Server Error (500):**
```json
{
  "success": false,
  "error": {
    "code": "SERVER_ERROR",
    "message": "An error occurred while processing your search."
  }
}
```

## Scalability Considerations

1. **Database Indexing**: Indexes on date fields, room_type_id, and foreign keys
2. **Caching Ready**: Services can be easily cached with Laravel's cache
3. **Property-First Design**: Schema supports multiple properties
4. **Configurable Rules**: Discounts and pricing are database-driven, not hardcoded

## Future Enhancements

- Add caching layer for frequent searches
- Implement rate limiting
- Add child pricing
- Support for promotions/coupons
- Multi-currency support
- Cache pricing calculations for better performance
