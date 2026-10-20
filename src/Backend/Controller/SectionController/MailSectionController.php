<?php

namespace DigitalMarketingFramework\Mail\Backend\Controller\SectionController;

use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionController;
use DigitalMarketingFramework\Core\Backend\Response\Response;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Mail\Manager\MailManagerInterface;
use DigitalMarketingFramework\Mail\Utility\MailUtility;
use Throwable;

/**
 * Sends an email of the reader's making, to check that the site can send mail at all. It goes
 * through the same mail manager as every other mail Anyrel sends, so a test email that arrives
 * rules out the mail setup when another mail does not.
 */
class MailSectionController extends SectionController
{
    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected MailManagerInterface $mailManager,
    ) {
        parent::__construct($keyword, $registry, 'mail', ['test', 'send']);
    }

    protected function testAction(): Response
    {
        return $this->render();
    }

    /**
     * A test email that cannot be sent ends on the backend error page with the reason. Mail
     * managers report failures in different ways, so anything thrown is turned into the one
     * exception the backend shows as a page.
     */
    protected function sendAction(): Response
    {
        $input = (array)($this->getParameters()['mail'] ?? []);

        try {
            $this->sendTestEmail(
                from: trim((string)($input['from'] ?? '')),
                to: trim((string)($input['to'] ?? '')),
                subject: trim((string)($input['subject'] ?? '')),
                body: (string)($input['body'] ?? ''),
            );
        } catch (DigitalMarketingFrameworkException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DigitalMarketingFrameworkException(sprintf('The test email could not be sent: %s', $e->getMessage()), (int)$e->getCode(), $e);
        }

        return $this->redirect('page.mail.test');
    }

    /**
     * Without a sender the mail manager decides, which is how the site's default sender is
     * tested.
     */
    protected function sendTestEmail(string $from, string $to, string $subject, string $body): void
    {
        $recipients = MailUtility::getAddressData($to);
        if ($recipients === []) {
            throw new DigitalMarketingFrameworkException('The test email needs a recipient.');
        }

        $message = $this->mailManager->createMessage();

        foreach (MailUtility::getAddressData($from, true) as $sender) {
            $message->addFrom($sender);
        }

        foreach ($recipients as $recipient) {
            $message->addTo($recipient);
        }

        $subject = MailUtility::sanitizeHeaderString($subject);
        $message->subject($subject !== '' ? $subject : 'Test email');
        $message->text($body);

        $this->mailManager->sendMessage($message);
    }
}
