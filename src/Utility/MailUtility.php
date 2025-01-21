<?php

namespace DigitalMarketingFramework\Mail\Utility;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Mail\Model\Data\Value\EmailValue;
use Symfony\Component\Mime\Address;

class MailUtility
{
    /**
     * getAddressData
     *
     * Input examples:
     * 'address@domain.tld'
     * 'Some Name <address@domain.tld>'
     * 'address@domain.tld, address-2@domain.tld'
     * 'Some Name <address@domain.tld>, address-2@domain.tld, Some Other Name <address-3@domain.tld>'
     * MultiValue(['address@domain.tld', 'Some Name <address@domain.tld>'])
     * EmailValue()
     * [EmailValue(), 'address@domain.tld']
     * MultiValue([EmailValue(), 'address@domain.tld'])
     *
     * @param string|ValueInterface $addresses
     * @param bool $onlyOneAddress
     *
     * @return array<Address>
     */
    public static function getAddressData($addresses, $onlyOneAddress = false): array
    {
        if ($addresses instanceof EmailValue) {
            $addresses = [$addresses];
        } elseif ($onlyOneAddress) {
            $addresses = [$addresses];
        } else {
            $addresses = GeneralUtility::castValueToArray($addresses);
        }

        $addresses = array_filter($addresses);

        $result = [];
        foreach ($addresses as $address) {
            $name = '';
            $email = '';
            if ($address instanceof EmailValue) {
                $name = $address->getName();
                $email = $address->getAddress();
            } elseif (preg_match('/^([^<]+)<([^>]+)>$/', (string)$address, $matches)) {
                // Some Name <some-address@domain.tld>
                $name = $matches[1];
                $email = $matches[2];
            } else {
                $email = $address;
            }

            $result[] = new Address($email, $name);
        }

        return $result;
    }

    /**
     * Checks string for suspicious characters
     *
     * @param string $string String to check
     *
     * @return string Valid or empty string
     */
    public static function sanitizeHeaderString(string $string): string
    {
        $pattern = '/[\\r\\n\\f\\e]/';
        if (preg_match($pattern, $string) > 0) {
            $string = '';
        }

        return $string;
    }
}
