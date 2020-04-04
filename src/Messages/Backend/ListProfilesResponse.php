<?php

namespace Omnipay\Mpay24\Messages\Backend;

/**
 * Lists matching profiles (customers).
 */

use Omnipay\Mpay24\Messages\AbstractMpay24Response;
use Omnipay\Mpay24\Messages\NotificationValuesTrait;

class ListProfilesResponse extends AbstractMpay24Response
{
    use NotificationValuesTrait;

    /**
     * Returns an array of arrays containing "customerID" and "updated" elements.
     */
    public function getProfiles()
    {
        return $this->getDataItem('profiles');
    }
}
