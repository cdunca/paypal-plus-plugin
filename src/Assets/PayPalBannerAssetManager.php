<?php

namespace WCPayPalPlus\Assets;

use WCPayPalPlus\PluginProperties;
use WCPayPalPlus\Setting\SharedRepository;

class PayPalBannerAssetManager
{
    use AssetManagerTrait;
    /**
     * @var PluginProperties
     */
    private $pluginProperties;
    /**
     * @var SharedRepository
     */
    private $sharedRepository;

    /**
     * AssetManager constructor.
     *
     * @param PluginProperties $pluginProperties
     * @param SharedRepository $sharedRepository
     */
    public function __construct(
        PluginProperties $pluginProperties,
        SharedRepository $sharedRepository
    ) {
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->pluginProperties = $pluginProperties;
        $this->sharedRepository = $sharedRepository;
    }

    /**
     * Register all the scripts related to Banner
     */
    public function registerScripts()
    {
        list($assetPath, $assetUrl) = $this->assetUrlPath();
        wp_register_script(
            'paypalplus-woocommerce-paypalBanner',
            "{$assetUrl}/public/js/paypalBanner.min.js",
            ['jquery'],
            filemtime("{$assetPath}/public/js/paypalBanner.min.js"),
            true
        );
    }

    /**
     * @deprecated
     * @see enqueueFrontEndScripts
     */
    public function enqueuePPBannerFrontEndScripts()
    {
        $this->enqueueFrontEndScripts();
    }

    /**
     * Enqueues the Banner frontend scripts
     * if conditions apply
     */
    public function enqueueFrontEndScripts()
    {
        if (!$this->isEnqueueAllowed()) {
            return;
        }

        $this->enqueueScripts();
    }

    /**
     * Returns false if the banner feature is disabled
     * Returns false if in a page where feature is disabled
     * @return bool
     */
    protected function isEnqueueAllowed()
    {
        if (!$this->isEnabledOption('banner_settings_enableBanner')) {
            return false;
        }
        if (!$this->isAllowedContext($this->optionalPagesSetting())) {
            return false;
        }
        return true;
    }

    /**
     * @param string $option  The option associated with the page to check
     *
     * @return bool Returns the option as a boolean
     */
    protected function isEnabledOption($option)
    {
        return wc_string_to_bool(
            get_option($option, 'no')
        );
    }

    /**
     * Check if in a page where the banner is enabled
     *
     * @param array $settings The optional pages setting
     *
     * @return bool True if we are in a page where the settings is enabled
     */
    protected function isAllowedContext($settings)
    {
        return (is_home() && isset($settings['show_home'])
                ? $settings['show_home'] : false)
            || (is_shop() && isset($settings['show_category'])
                ? $settings['show_category'] : false)
            || (is_search() && isset($settings['show_search'])
                ? $settings['show_search'] : false)
            || (is_product() && isset($settings['show_product'])
                ? $settings['show_product'] : false)
            || (is_cart() && isset($settings['show_cart'])
                ? $settings['show_cart'] : false)
            || (is_checkout() && isset($settings['show_checkout'])
                ? $settings['show_checkout'] : false);
    }

    /**
     * Gets the option values for the optional pages.
     * @return array
     */
    protected function optionalPagesSetting()
    {
        return [
            'show_home' => $this->isEnabledOption(
                'banner_settings_home'
            ),
            'show_category' => $this->isEnabledOption(
                'banner_settings_products'
            ),
            'show_search' => $this->isEnabledOption(
                'banner_settings_search'
            ),
            'show_product' => $this->isEnabledOption(
                'banner_settings_product_detail'
            ),
            'show_cart' => $this->isEnabledOption(
                'banner_settings_cart'
            ),
            'show_checkout' => $this->isEnabledOption(
                'banner_settings_checkout'
            ),
        ];
    }

    /**
     * Enqueues the scripts to the footer
     * and adds the markup to show on page.
     */
    protected function enqueueScripts()
    {
        add_action(
            'wp_footer',
            function () {
                $this->bannerFrontEndScript();
            }
        );
        $this->placeBannerOnPage();
    }

    /**
     * Enqueues the front end script for the banner feature
     * loads settings data
     */
    protected function bannerFrontEndScript()
    {
        $settings = $this->bannerSettings();
        list($assetPath, $assetUrl) = $this->assetUrlPath();
        wp_enqueue_script(
            'paypalplus-woocommerce-paypalBanner',
            "{$assetUrl}/public/js/paypalBanner.min.js",
            ['jquery'],
            filemtime("{$assetPath}/public/js/paypalBanner.min.js"),
            true
        );
        $this->loadScriptsData(
            'paypalplus-woocommerce-paypalBanner',
            'paypalBannerFrontData',
            [
                'settings' => $settings,
            ]
        );
    }

    /**
     * Retrieves the settings data
     * related to the banner feature
     * @return array
     */
    protected function bannerSettings()
    {
        $scriptUrl = $this->paypalScriptUrl();
        $amount = $this->calculateAmount();

        return [
            'amount' => $amount,
            'script_url' => $scriptUrl,
            'enabled_banner' => $this->isEnabledOption(
                'banner_settings_enableBanner'
            ),
            'optional_pages' => $this->optionalPagesSetting(),
            'style' => [
                'layout' => get_option('banner_settings_layout'),
                'logo' => [
                    'type' => get_option('banner_settings_textSize'),
                    'color' => get_option('banner_settings_textColor'),
                ],
                'color' => get_option('banner_settings_flexColor'),
                'ratio' => get_option('banner_settings_flexSize'),
            ],
        ];
    }

    /**
     * @return string The script url with clientID and currency
     */
    protected function paypalScriptUrl()
    {
        $clientId = get_option('banner_settings_clientID');
        if (empty($clientId)) {
            $clientId = $this->sharedRepository->clientIdProduction();
            update_option('banner_settings_clientID', $clientId);
        }
        $currency = get_woocommerce_currency();
        if (!isset($clientId) || !isset($currency)) {
            return '';
        }

        return "https://www.paypal.com/sdk/js?client-id={$clientId}&components=messages&currency={$currency}";
    }

    /**
     * Retrieves the cart amount and adds it to the product price
     * if we are in a product page.
     * @return float The total amount
     */
    protected function calculateAmount()
    {
        wc_load_cart();
        $amount = WC()->cart->get_total('edit');
        if (is_product() && is_numeric(wc_get_product()->get_price('edit'))) {
            return $amount + (float)wc_get_product()->get_price('edit');
        }

        return $amount;
    }

    /**
     * Adds action to place the banner on desired location
     */
    protected function placeBannerOnPage()
    {
        $hook = $this->hookForCurrentPage();
        add_action(
            $hook,
            function () {
                ?>
                <div id="paypal-credit-banner"></div>
                <?php

            }
        );
        if (is_home()) {
            add_filter(
                'the_content',
                function ($content) {
                    return '<div id="paypal-credit-banner"></div>' . $content;
                }
            );
        }
        if (is_search()) {
            do_action('show_paypal_banner_search');
        }
    }

    /**
     * @return string The hook to use to place the banner
     */
    protected function hookForCurrentPage()
    {
        if (is_cart()) {
            return 'woocommerce_before_cart';
        }
        if (is_checkout()) {
            return 'woocommerce_checkout_before_customer_details';
        }
        if (is_product()) {
            return 'woocommerce_before_single_product_summary';
        }
        if (is_shop() || is_category() || is_search()) {
            return 'woocommerce_before_shop_loop';
        }
    }
}
