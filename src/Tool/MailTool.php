<?php

namespace Starfruit\BuilderBundle\Tool;

use Pimcore\Mail;
use Pimcore\Model\Document\Email;

class MailTool
{
    public static function send($document, $mailAddress, $params, $subject = null)
    {
        try {
            if ($mailAddress) {
                $document = $document instanceof Email ? $document : Email::getByPath($document);
                $mail = new Mail();
                $mail->setDocument($document);
                if ($subject) {
                    $mail->subject($subject);
                }
                $mail->setParams($params);
                
                $addresses = explode(',', $mailAddress);
                foreach ($addresses as $key => $address) {
                    if ($key == 0) {
                        $mail->to($address);
                    } else {
                        $mail->addTo($address);
                    }
                }

                $mail->send();
            }
        } catch (\Throwable $e) {
        }
    }
}
