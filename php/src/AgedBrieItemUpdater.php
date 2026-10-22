<?php

declare(strict_types=1);

namespace GildedRose;

final class AgedBrieItemUpdater implements ItemUpdater
{
    private const INCREASE_RATE = 1;

    public function update(Item $item): void
    {
        $this->increaseQuality($item);
        $item->sellIn = $item->sellIn - 1;
        if ($item->sellIn < 0) {
            $this->increaseQuality($item);
        }
    }

    private function increaseQuality(Item $item): void
    {
        if ($item->quality < 50) {
            $item->quality = $item->quality + self::INCREASE_RATE;
        }
    }
}
