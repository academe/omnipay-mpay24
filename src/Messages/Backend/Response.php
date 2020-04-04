<?php

namespace Omnipay\Mpay24\Messages\Backend;

/**
 * Data provided is the raw data from the API, with upper-case keys.
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Mpay24\Messages\NotificationValuesTrait;

class Response extends AbstractMpay24Response
{
    use NotificationValuesTrait;
}
