<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Contracts\Support\Arrayable;

class DiscountDTO implements Arrayable
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly string $type,
        public readonly float $value,
        public readonly float $amount,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'value' => $this->value,
            'amount' => round($this->amount, 2),
        ];
    }
}
