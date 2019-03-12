<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Refund;

use Inpsyde\Lib\PayPal\Exception\PayPalConnectionException;
use Inpsyde\Lib\PayPal\Rest\ApiContext;
use Psr\Log\LoggerInterface;
use WCPayPalPlus\Order\OrderStatuses;

/**
 * Class Refunder
 *
 * @package WCPayPalPlus\Refund
 */
class Refunder
{
    /**
     * RefundData object.
     *
     * @var ApiContext
     */
    private $context;

    /**
     * PayPal Api Context object.
     *
     * @var RefundData
     */
    private $refund_data;

    /**
     * @var OrderStatuses
     */
    private $orderStatuses;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * WCRefund constructor.
     * @param RefundData $refund_data
     * @param ApiContext $context
     * @param OrderStatuses $orderStatuses
     * @param LoggerInterface $logger
     */
    public function __construct(
        RefundData $refund_data,
        ApiContext $context,
        OrderStatuses $orderStatuses,
        LoggerInterface $logger
    ) {

        $this->context = $context;
        $this->refund_data = $refund_data;
        $this->orderStatuses = $orderStatuses;
        $this->logger = $logger;
    }

    /**
     * Execute the refund via PayPal API
     *
     * @return bool
     */
    public function execute()
    {
        $sale = $this->refund_data->get_sale();
        $refund = $this->refund_data->get_refund();

        try {
            $refundedSale = $sale->refundSale($refund, $this->context);
            $isOrderCompleted = $this->orderStatuses->orderStatusIs(
                $refundedSale->state,
                OrderStatuses::ORDER_STATUS_COMPLETED
            );

            $isOrderCompleted and $this
                ->refund_data
                ->get_success_handler($refundedSale->getId())
                ->execute();
        } catch (PayPalConnectionException $exc) {
            $this->logger->error($exc);
            return false;
        }

        return true;
    }
}
