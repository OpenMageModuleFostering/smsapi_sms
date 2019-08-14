<?php

use SMSApi\Api\SenderFactory;
require_once dirname(__DIR__) . '/smsapi-classes/AbstractsSmsapi.php';

class Sender
{
    private $sender;

    private $settings;

    private $_default;

    private $senderFactory;

    public function __construct(SenderFactory $senderFactory, $sender = NULL, $settings = NULL, $_default)
    {
        $this->senderFactory = $senderFactory;
        $this->sender = $sender;
        $this->settings = $settings;
        $this->_default = $_default;
    }

    public function getSenders()
    {
        $smsApi = $this->senderFactory;

        $actionList = $smsApi->actionList();
        $response = $actionList->execute();

        if (!empty($this->settings['sender'])) {
            $names[$this->settings['sender']] = $this->settings['sender'];
            $names['DEFAULT'] = $this->_default;
        } else {
            if ($this->sender != null) {
                $names[$this->sender] = $this->sender;
                $names['DEFAULT'] = $this->_default;
            } else {
                $names['DEFAULT'] = $this->_default;
            }
        }

        if (!empty($this->settings)) {
            foreach ($response->getList() as $status) {
                if ($status->getStatus() == 'ACTIVE' && $status->getName() != $this->sender && $status->getName() != $this->settings['sender']) {
                    $names[$status->getName()] = $status->getName();
                }
            }
        } else {
            foreach ($response->getList() as $status) {
                if ($status->getStatus() == 'ACTIVE' && $status->getName() != $this->sender) {
                    $names[$status->getName()] = $status->getName();
                }
            }
        }
        return $names;
    }
}