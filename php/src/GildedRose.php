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
        $agedBrieUpdater = new AgedBrieItemUpdater();
        $backstagePassesUpdater = new BackstagePassesItemUpdater();
        $conjuredUpdater = new ConjuredItemUpdater();
        $defaultUpdater = new DefaultItemUpdater();
        foreach ($this->items as $item) {
            match (ItemName::tryFrom($item->name)) {
                ItemName::Sulfuras => null,
                ItemName::AgedBrie => $agedBrieUpdater->update($item),
                ItemName::BackstagePasses => $backstagePassesUpdater->update($item),
                ItemName::Conjured => $conjuredUpdater->update($item),
                null => $defaultUpdater->update($item),
            };
        }
    }
}
