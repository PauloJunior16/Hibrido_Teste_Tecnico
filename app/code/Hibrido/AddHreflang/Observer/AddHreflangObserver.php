<?php

namespace Hibrido\AddHreflang\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Cms\Model\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AddHreflangObserver implements ObserverInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @ingeritdoc
     */
    public function execute(Observer $observer)
    {
        $page = $this->pageFactory->create()->load('about-us', 'identifier');
        if (!$page->getId()) {
            return;
        }

        $storeIds = $page->getStoreId();
        if (is_array($storeIds) && count($storeIds) > 1) {
            $layout = $observer->getData('layout');
            $head = $layout->getBlock('head');

            foreach ($storeIds as $storeId) {
                $store = $this->storeManager->getStore($storeId);
                $baseUrl = $store->getBaseUrl();
                $cmsPageUrl = $baseUrl . 'about-us/';

                $storeLanguage = $this->getStoreLanguageCode($storeId);
                $head->addLinkRel('alternate', $cmsPageUrl, ['hreflang' => $storeLanguage]);
            }
        }
    }

    /**
     * Function to retrieve the store locale
     * @param $storeId
     * @return mixed
     */
    protected function getStoreLanguageCode($storeId)
    {
        return $this->scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
