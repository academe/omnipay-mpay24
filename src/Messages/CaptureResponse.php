<?php

namespace Omnipay\Mpay24\Messages;

/**
 * Data provided is the raw data from the API, with upper-case keys.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Response;

class CaptureResponse extends AbstractMpay24Response
{
    use NotificationValuesTrait;
}
