<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GildedRose\GildedRose
 * @covers \GildedRose\Item
 *
- All `items` have a `SellIn` value which denotes the number of days we have to sell the `items`
- All `items` have a `Quality` value which denotes how valuable the item is
- At the end of each day our system lowers both values for every item

Pretty simple, right? Well this is where it gets interesting:

- Once the sell by date has passed, `Quality` degrades twice as fast
- The `Quality` of an item is never negative
- __"Aged Brie"__ actually increases in `Quality` the older it gets
- The `Quality` of an item is never more than `50`
- __"Sulfuras"__, being a legendary item, never has to be sold or decreases in `Quality`
- __"Backstage passes"__, like aged brie, increases in `Quality` as its `SellIn` value approaches;
        - `Quality` increases by `2` when there are `10` days or less and by `3` when there are `5` days or less but
        - `Quality` drops to `0` after the concert

We have recently signed a supplier of conjured items. This requires an update to our system:

- __"Conjured"__ items degrade in `Quality` twice as fast as normal items

Feel free to make any changes to the `UpdateQuality` method and add any new code as long as everything
still works correctly. However, do not alter the `Item` class or `Items` property as those belong to the
goblin in the corner who will insta-rage and one-shot you as he doesn't believe in shared code
ownership (you can make the `UpdateQuality` method and `Items` property static if you like, we'll cover
for you).

Just for clarification, an item can never have its `Quality` increase above `50`, however __"Sulfuras"__ is a
legendary item and as such its `Quality` is `80` and it never alters.
 */

class GildedRoseTest extends TestCase
{
    private const MAX_NORMAL_QUALITY = 50;

    private const MAX_LEGENDARY_QUALITY = 80;

    public function testItemToStringMagicMethod(): void
    {
        $item = new Item('Normal item', 1, 1);
        $this->assertSame('Normal item, 1, 1', (string) $item);
    }

    public function testQualityDegradesOnePerDaybeforeSellByDate(): void
    {
        $sellIn = 1;
        $dailyQualityDegradation = 1;
        $item = new Item('Normal item', $sellIn, self::MAX_NORMAL_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(0, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY - $dailyQualityDegradation, $item->quality);
    }

    public function testQualityDegradesTwicePerDayAfterSellByDate(): void
    {
        $sellIn = 0;
        $dailyQualityDegradation = 2;
        $item = new Item('Normal item', $sellIn, self::MAX_NORMAL_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(-1, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY - $dailyQualityDegradation, $item->quality);
    }

    public function testQualityNeverNegative(): void
    {
        $sellIn = 1;
        $item = new Item('Normal item', $sellIn, 0);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(0, $item->quality);
    }

    public function testAgedBrieIncreasesQualityOnceEveryDay(): void
    {
        $sellIn = 1;
        $item = new Item('Aged Brie', $sellIn, self::MAX_NORMAL_QUALITY - 1);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(0, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testAgedBrieIncreasesQualityTwiceEveryDayAfterSellByDate(): void
    {
        $sellIn = 0;
        $item = new Item('Aged Brie', $sellIn, self::MAX_NORMAL_QUALITY - 2);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(-1, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testQualityNeverMoreThanMaxNormalQuality(): void
    {
        $sellIn = 1;
        $item = new Item('Aged Brie', $sellIn, self::MAX_NORMAL_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testSulfurasNeverAltersQualityNorSellIn(): void
    {
        $sellIn = 1;
        $item = new Item('Sulfuras, Hand of Ragnaros', $sellIn, self::MAX_LEGENDARY_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(self::MAX_LEGENDARY_QUALITY, $item->quality);
        $this->assertSame(1, $item->sellIn);
        $gildedRose->updateQuality();
        $this->assertSame(self::MAX_LEGENDARY_QUALITY, $item->quality);
        $this->assertSame(1, $item->sellIn);
    }

    public function testBackstagePassesIncreaseQualityBy1WhenSellInIsGreaterThan10(): void
    {
        $sellIn = 11;
        $item = new Item('Backstage passes to a TAFKAL80ETC concert', $sellIn, self::MAX_NORMAL_QUALITY - 1);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(10, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testBackstagePassesIncreaseQualityBy2WhenSellInIs10OrLess(): void
    {
        $sellIn = 10;
        $item = new Item('Backstage passes to a TAFKAL80ETC concert', $sellIn, self::MAX_NORMAL_QUALITY - 2);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(9, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testBackstagePassesIncreaseQualityBy3WhenSellInIs5OrLess(): void
    {
        $sellIn = 5;
        $item = new Item('Backstage passes to a TAFKAL80ETC concert', $sellIn, self::MAX_NORMAL_QUALITY - 3);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(4, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY, $item->quality);
    }

    public function testBackstagePassesQualityBecomesZeroAfterSellInDate(): void
    {
        $sellIn = 0;
        $item = new Item('Backstage passes to a TAFKAL80ETC concert', $sellIn, self::MAX_NORMAL_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(-1, $item->sellIn);
        $this->assertSame(0, $item->quality);
    }

    public function testConjuredItemsDegradeTwiceAsFastAsNormalItems(): void
    {
        $sellIn = 1;
        $dailyQualityDegradation = 2;
        $item = new Item('Conjured Mana Cake', $sellIn, self::MAX_NORMAL_QUALITY);
        $gildedRose = new GildedRose([$item]);
        $gildedRose->updateQuality();
        $this->assertSame(0, $item->sellIn);
        $this->assertSame(self::MAX_NORMAL_QUALITY - $dailyQualityDegradation, $item->quality);
    }
}
