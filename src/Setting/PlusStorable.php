<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Setting;

/**
 * Interface PlusStorable
 * @package WCPayPalPlus\Setting
 */
interface PlusStorable extends Storable
{
    // TODO Remove the _NAME suffix, it's additional information unneeded.
    const OPTION_DISABLE_GATEWAY_OVERRIDE_NAME = 'disable_gateway_override';
    const OPTION_TEST_MODE_NAME = 'testmode';
    const OPTION_LEGAL_NOTE_NAME = 'legal_note';

    const OPTION_PROFILE_ID_SANDBOX_NAME = 'sandbox_experience_profile_id';
    const OPTION_PROFILE_ID_LIVE_NAME = 'live_experience_profile_id';

    const OPTION_CANCEL_URL_NAME = 'cancel_url';
    const OPTION_CANCEL_CUSTOM_URL_NAME = 'cancel_custom_url';

    const OPTION_INVOICE_PREFIX = 'invoice_prefix';

    const OPTION_CLIENT_ID = 'rest_client_id';
    const OPTION_SECRET_ID = 'rest_secret_id';

    const OPTION_CLIENT_ID_SANDBOX = 'rest_client_id_sandbox';
    const OPTION_SECRET_ID_SANDBOX = 'rest_secret_id_sandbox';

    /**
     * @return bool
     */
    public function isDisableGatewayOverrideEnabled();

    /**
     * @return string
     */
    public function legalNotes();

    /**
     * @return string
     */
    public function experienceProfileId();

    /**
     * @return string
     */
    public function cancelUrl();

    /**
     * @return string
     */
    public function cancelCustomUrl();

    /**
     * @return string
     */
    public function invoicePrefix();
}
