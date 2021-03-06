<?php
/**
 * Copyright © 2017 H&O E-commerce specialisten B.V. (http://www.h-o.nl/)
 * See LICENSE.txt for license details.
 */

namespace Ho\Templatehints\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Developer\Helper\Data as DeveloperHelper;

class Config extends AbstractHelper
{
    /** @var AppState $appState */
    private $appState;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var DeveloperHelper $developerHelper */
    private $developerHelper;

    /**
     * @param Context               $context
     * @param AppState              $appState
     * @param StoreManagerInterface $storeManager
     * @param DeveloperHelper       $developerHelper
     */
    public function __construct(
        Context $context,
        AppState $appState,
        StoreManagerInterface $storeManager,
        DeveloperHelper $developerHelper
    ) {
        parent::__construct($context);

        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->developerHelper = $developerHelper;
    }

    /**
     * Check if the hints can be displayed.
     *
     * It will check if the url parameter is present.
     * For production mode it will also check if the IP-address is in Developer Client Restrictions.
     *
     * @return bool
     */
    public function isHintEnabled()
    {
        $isParamPresent = $this->_request->getParam('ath', false) === '1';

        if ($isParamPresent) {
            $applicationMode = $this->appState->getMode();
            $storeId = $this->storeManager->getStore()->getId();

            if ($applicationMode === AppState::MODE_DEVELOPER || $this->developerHelper->isDevAllowed($storeId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Simply retrieves config values already stored within the system.
     *
     * @param string $field The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @return mixed
     */
    public function getConfigValue($field)
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }
}
