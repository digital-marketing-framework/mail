<?php

namespace DigitalMarketingFramework\Mail;

use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\Backend\Section\Section;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Mail\Backend\Controller\SectionController\MailSectionController;
use DigitalMarketingFramework\Mail\DataProcessor\ValueSource\EmailValueSource;
use DigitalMarketingFramework\Mail\Manager\DefaultMailManager;
use DigitalMarketingFramework\Mail\Manager\MailManagerInterface;

class MailInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::CORE => [
            ValueSourceInterface::class => [
                EmailValueSource::class,
            ],
            SectionControllerInterface::class => [
                MailSectionController::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(
        protected ?MailManagerInterface $mailManager = null,
        string $packageAlias = '',
    ) {
        parent::__construct('mail', '1.0.0', $packageAlias);
    }

    protected function getAdditionalPluginArguments(string $interface, string $pluginClass, RegistryInterface $registry): array
    {
        if ($pluginClass === MailSectionController::class) {
            return [$this->mailManager ?? $registry->createObject(DefaultMailManager::class)];
        }

        return parent::getAdditionalPluginArguments($interface, $pluginClass, $registry);
    }

    protected function getBackendSections(): array
    {
        return [
            new Section(
                'Mail',
                'CORE',
                'page.mail.test',
                'Send a test email',
                'PKG:digital-marketing-framework/mail/res/assets/icons/dashboard-mail.svg',
                'Show',
                95
            ),
        ];
    }
}
