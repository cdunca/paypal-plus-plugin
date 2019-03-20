<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Utils;

/**
 * Trait PriceFormatterTrait
 * @package WCPayPalPlus\Utils
 */
trait PriceFormatterTrait
{
    /**
     * @param float $price The un-formatted price.
     * @return float
     */
    private function format($price)
    {
        $decimals = wc_get_price_decimals();

        if (!$this->currencyHasDecimal()) {
            $decimals = 0;
        }

        return wc_format_decimal($price, $decimals);
    }

    /**
     * Rounds a price to 2 decimals.
     *
     * @param float $price The item price.
     * @return float
     */
    private function round($price)
    {
        $precision = wc_get_price_decimals();

        if (!$this->currencyHasDecimal()) {
            $precision = 0;
        }

        return round($price, $precision);
    }

    /**
     * Checks if the currency supports decimals.
     *
     * @return bool
     */
    private function currencyHasDecimal()
    {
        return in_array(get_woocommerce_currency(), ['HUF', 'JPY', 'TWD'], true);
    }
}