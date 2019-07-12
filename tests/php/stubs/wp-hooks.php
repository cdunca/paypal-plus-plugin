<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!function_exists('apply_filters')) {
    /**
     * @param $string
     * @param $callback
     */
    function apply_filters($string, $callback)
    {
        $string and $callback();
    }
}
