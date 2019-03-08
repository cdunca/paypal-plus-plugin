<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus;

/**
 * Interface Renderable
 * @package WCPayPalPlus\ExpressCheckout
 */
interface Renderable
{
    /**
     * Render the button view
     *
     * @return void
     */
    public function render();
}