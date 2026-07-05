<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\Hotel;
use Illuminate\Support\Collection;

class RecentlyViewedService
{
    private const SESSION_KEY = 'recently_viewed';
    private const MAX_ITEMS = 8;

    /**
     * Record that the given item was just viewed, moving it to the front of the list.
     */
    public function record(string $type, int $id): void
    {
        $items = session(self::SESSION_KEY, []);

        $items = array_values(array_filter($items, fn($item) => !($item['type'] === $type && $item['id'] === $id)));

        array_unshift($items, ['type' => $type, 'id' => $id]);

        session([self::SESSION_KEY => array_slice($items, 0, self::MAX_ITEMS)]);
    }

    /**
     * Resolve the recently viewed list into actual models, optionally excluding one item.
     */
    public function get(?string $excludeType = null, ?int $excludeId = null): Collection
    {
        $items = session(self::SESSION_KEY, []);

        $items = array_filter($items, fn($item) => !($item['type'] === $excludeType && $item['id'] === $excludeId));

        return collect($items)
            ->map(function ($item) {
                $model = match ($item['type']) {
                    'destination' => Destination::find($item['id']),
                    'hotel' => Hotel::find($item['id']),
                    default => null,
                };

                return $model ? ['type' => $item['type'], 'model' => $model] : null;
            })
            ->filter()
            ->values();
    }
}
