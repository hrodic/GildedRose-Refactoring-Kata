<?php

declare(strict_types=1);

namespace GildedRose;

final class BackstagePassesItemUpdater implements ItemUpdater
{
    private const INCREASE_RATE = 1;

    public function update(Item $item): void
    {
        $item->sellIn = $item->sellIn - 1;
        if ($item->sellIn < 0) {
            $item->quality = 0;
            return; // Backstage passes quality drops to 0 after the concert guard clause
        }

        $this->increaseQuality($item);
        if ($item->sellIn < 10) {
            $this->increaseQuality($item);
        }
        if ($item->sellIn < 5) {
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
