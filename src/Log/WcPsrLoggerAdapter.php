<?php
/*
 * This file is part of the PayPal PLUS for WooCommerce package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WCPayPalPlus\Log;

use Inpsyde\Lib\Psr\Log\AbstractLogger;
use Inpsyde\Lib\Psr\Log\LogLevel;

class WcPsrLoggerAdapter extends AbstractLogger
{
    const LOGGER_SOURCE = 'paypal_plus';

    /**
     * @var array
     */
    private $psrWcLoggingLevels = array(
        LogLevel::EMERGENCY => \WC_Log_Levels::EMERGENCY,
        LogLevel::ALERT => \WC_Log_Levels::ALERT,
        LogLevel::CRITICAL => \WC_Log_Levels::CRITICAL,
        LogLevel::ERROR => \WC_Log_Levels::ERROR,
        LogLevel::WARNING => \WC_Log_Levels::WARNING,
        LogLevel::NOTICE => \WC_Log_Levels::NOTICE,
        LogLevel::INFO => \WC_Log_Levels::INFO,
        LogLevel::DEBUG => \WC_Log_Levels::DEBUG
    );

    /**
     * @var \WC_Logger_Interface
     */
    private $wcLogger;

    /**
     * @var string
     */
    private $className = '';

    /**
     * @var string
     */
    private $loggingLevel;

    /**
     * WcPsrLoggerAdapter constructor.
     *
     * @param \WC_Logger_Interface $wcLogger
     * @param string $loggingLevel
     */
    public function __construct(\WC_Logger_Interface $wcLogger, $loggingLevel = \WC_Log_Levels::INFO)
    {
        $this->wcLogger = $wcLogger;
        \assert(in_array($loggingLevel, $this->psrWcLoggingLevels, true));
        $this->loggingLevel = $loggingLevel;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $wcLevel = $level;
        if (isset($this->psrWcLoggingLevels[$level])) {
           $wcLevel = $this->psrWcLoggingLevels[$level];
        }

        if (\WC_Log_Levels::get_level_severity($wcLevel) < \WC_Log_Levels::get_level_severity($this->loggingLevel)) {
            return;
        }

        $context['source'] = self::LOGGER_SOURCE;
        if ($this->className) {
            $context['SDKClassName'] = $this->className;
        }

        $this->wcLogger->log($wcLevel, $message, $context);
    }

    /**
     * @param string $className
     */
    public function setName($className)
    {
        \assert(\class_exists($className));

        $this->className = $className;
    }
}