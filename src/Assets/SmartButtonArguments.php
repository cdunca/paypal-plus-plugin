<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Assets;

use WC_Admin_Settings as Settings;
use WCPayPalPlus\Setting\PlusStorable;

/**
 * Class SmartButtonArguments
 * @package WCPayPalPlus\Assets
 */
class SmartButtonArguments
{
    const ENVIRONMENT_SANDBOX = 'sandbox';
    const ENVIRONMENT_PRODUCTION = 'production';

    const DEFAULT_CURRENCY = 'EUR';

    const FILTER_LOCALE = 'woopaypalplus.express_checkout_button_locale';
    const DISABLED_FUNDING = [
        'card',
        'credit',
    ];

    /**
     * @var PlusStorable
     */
    private $settingRepository;

    /**
     * SmartButtonArguments constructor.
     * @param PlusStorable $settingRepository
     */
    public function __construct(PlusStorable $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * Return the Script Arguments as an array
     *
     * @return array
     */
    public function toArray()
    {
        $currency = $this->wooCommerceSettings('currency', self::DEFAULT_CURRENCY);
        $locale = get_locale();

        /**
         * Filter locale
         *
         * Allow third parties to filter the locale if needed.
         *
         * @param string $locale
         */
        $locale = apply_filters(self::FILTER_LOCALE, $locale);

        return [
            'currency' => $currency,
            'intent' => 'authorize',
            'payment_method' => 'paypal',
            'env' => $this->environment(),
            'commit' => true,
            'locale' => $locale,
            'funding' => [
                'disallowed' => self::DISABLED_FUNDING,
            ],
            'style' => [
                'color' => 'gold',
                'shape' => 'rect',
                'size' => 'responsive',
                'branding' => true,
                'tagline' => false,
                'layout' => 'vertical',
            ],
            'redirect_urls' => [
                'cancel_url' => 'http://paypalplus.local/cart/',
                'return_url' => 'http://paypalplus.local/checkout/',
            ],
        ];
    }

    /**
     * Retrieve a WooCommerce Option by the given name
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private function wooCommerceSettings($name, $default = null)
    {
        assert(is_string($name));

        return Settings::get_option($name, $default);
    }

    /**
     * Retrieve the environment
     *
     * @return string
     */
    private function environment()
    {
        return $this->settingRepository->isSandboxed() ? 'sandbox' : 'production';
    }
}