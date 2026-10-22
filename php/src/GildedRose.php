<?php

declare(strict_types=1);

namespace GildedRose;

final class GildedRose
{
    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    ) {
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            // branch for sulfuras
            if ($item->name === ItemName::Sulfuras->value) {
                continue;
            }
            // branch for aged brie
            if ($item->name === ItemName::AgedBrie->value) {
                $this->updateAgedBrie($item);
                continue;
            }
            // branch for backstage passes
            if ($item->name === ItemName::BackstagePasses->value) {
                $this->updateBackstagePasses($item);
                continue;
            }
            // default branch
            $this->updateDefault($item);
        }
    }

    private function updateAgedBrie(Item $item): void
    {
        if ($item->quality < 50) {
            $item->quality = $item->quality + 1;
        }
        $item->sellIn = $item->sellIn - 1;
        if ($item->sellIn < 0) {
            if ($item->quality < 50) {
                $item->quality = $item->quality + 1;
            }
        }
    }

    private function updateDefault(Item $item): void
    {
        if ($item->quality > 0) {
            $item->quality = $item->quality - 1;
        }

        $item->sellIn = $item->sellIn - 1;

        if ($item->sellIn < 0) {
            if ($item->quality > 0) {
                $item->quality = $item->quality - 1;
            }
        }
    }

    private function updateBackstagePasses(Item $item): void
    {
        if ($item->quality < 50) {
            $item->quality = $item->quality + 1;

            if ($item->sellIn < 11) {
                if ($item->quality < 50) {
                    $item->quality = $item->quality + 1;
                }
            }
            if ($item->sellIn < 6) {
                if ($item->quality < 50) {
                    $item->quality = $item->quality + 1;
                }
            }
        }

        $item->sellIn = $item->sellIn - 1;

        if ($item->sellIn < 0) {
            $item->quality = $item->quality - $item->quality;
        }
    }
}
