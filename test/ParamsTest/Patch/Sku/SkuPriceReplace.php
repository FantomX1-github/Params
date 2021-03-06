<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Sku;

use Params\FirstRule\GetInt;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SafeAccess;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MaxLength;

class SkuPriceReplace
{
    use SafeAccess;

    /** @var int */
    private $sku_id;

//    /** @var int */
//    private $sku_price_id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var int */
    private $price_eur;

    /** @var int */
    private $price_gbp;

    /** @var int */
    private $price_usd;

    /**
     *
     * @param int $sku_id
     * @param string $name
     * @param string $description
     * @param int $price_eur
     * @param int $price_gbp
     * @param int $price_usd
     */
    public function __construct(
        int $sku_id,
        string $name,
        string $description,
        int $price_eur,
        int $price_gbp,
        int $price_usd
    ) {
        $this->sku_id = $sku_id;
        $this->name = $name;
        $this->description = $description;
        $this->price_eur = $price_eur;
        $this->price_gbp = $price_gbp;
        $this->price_usd = $price_usd;
    }


    public static function getRules()
    {
        return [
            'sku_id' => [
                new GetInt()
            ],
            // TODO - this should be  a test operation
//            'sku_price_id' => [
//                new GetInt(),
//            ],
            'name' => [
                new GetString(),
                new MinLength(8),
                new MaxLength(256),
            ],
            'description' => [
                new GetString(),
                new MinLength(8),
                new MaxLength(256),
            ],

            'price_eur' => [
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ],

            'price_gbp'  => [
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ],
            'price_usd' => [
                new GetInt(),
                new MinIntValue(10000),
                new MaxIntValue(1000000)
            ],
        ];
    }

    /**
     * @return int
     */
    public function getSkuId(): int
    {
        return $this->sku_id;
    }

//    /**
//     * @return int
//     */
//    public function getSkuPriceId(): int
//    {
//        return $this->sku_price_id;
//    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPriceEur(): int
    {
        return $this->price_eur;
    }

    /**
     * @return int
     */
    public function getPriceGbp(): int
    {
        return $this->price_gbp;
    }

    /**
     * @return int
     */
    public function getPriceUsd(): int
    {
        return $this->price_usd;
    }
}
