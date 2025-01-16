<?php

namespace DigitalMarketingFramework\Mail;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Mail\DataProcessor\ValueSource\EmailValueSource;

class MailInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::CORE => [
            ValueSourceInterface::class => [
                EmailValueSource::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('mail', '1.0.0', $packageAlias);
    }
}
