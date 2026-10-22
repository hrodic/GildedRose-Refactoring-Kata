<?php

declare(strict_types=1);

namespace GildedRose;

final class DefaultItemUpdater implements ItemUpdater
{
    private const DEGRADATION_RATE = 1;

    public function update(Item $item): void
    {
        $this->degradeQuality($item);
        $item->sellIn = $item->sellIn - 1;
        if ($item->sellIn < 0) {
            $this->degradeQuality($item);
        }
    }

    private function degradeQuality(Item $item): void
    {
        if ($item->quality > 0) {
            $item->quality = max(0, $item->quality - self::DEGRADATION_RATE);
        }
    }
}
