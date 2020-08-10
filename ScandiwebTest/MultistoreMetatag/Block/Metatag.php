<?php
namespace ScandiwebTest\MultistoreMetatag\Block;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Helper\Page;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Metatag
 * @package ScandiwebTest\MultistoreMetatag\Block
 */
class Metatag extends Template
{
    /** @var PageInterface */
    protected $page;

    /** @var Resolver */
    protected $localeResolver;

    /** @var PageRepositoryInterface */
    protected $pageRepository;

    /** @var Page */
    protected $cmsPageHelper;

    /**
     * Metatag constructor.
     * @param Context $context
     * @param Resolver $localeResolver
     * @param PageRepositoryInterface $pageRepository
     * @param Page $cmsPageHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $localeResolver,
        PageRepositoryInterface $pageRepository,
        Page $cmsPageHelper,
        array $data = []
    ) {
        $this->localeResolver = $localeResolver;
        $this->pageRepository = $pageRepository;
        $this->cmsPageHelper = $cmsPageHelper;
        parent::__construct($context, $data);
    }

    /**
     * After checking if the page is Cms and page is used in more then 1 store
     * It calls the foreach to pass by every store and concatenates all the metatags inside 1 variable so it can be
     * Returned to the head of the DOM
     * @return string
     */
    protected function _toHtml()
    {
        if($this->isPageCms() && $this->isPageUsedInMultiStores()) {
            $metaTagHtml = '';
            foreach ($this->_storeManager->getStores() as $store) {
                $storeBaseUrl = $store->getBaseUrl();
                $metaTagHtml .= '<link rel="alternate" hreflang="' . $this->getStoreLanguage($store) . '" href="' . $storeBaseUrl . $this->page->getIdentifier() . '" />';
            }
            return $metaTagHtml;
        }

        return '';
    }

    /**
     * Recieve $store so it can search inside core_config_data for the right store.
     * @param StoreInterface $store
     * @return mixed
     */
    public function getStoreLanguage($store) {
        $locale = $this->_scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId());
        return $locale;
    }

    /**
     * Since this is a block that will be called in every page
     * it checks first if is of the correct module and has the correct param
     * and in the next function it loads the cms page so it dont mess up with the performance
     * @return bool
     */
    protected function isPageCms() {
        $moduleName = $this->getRequest()->getModuleName();
        if($moduleName === 'cms' && $this->getPageId()) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether the page has different Stores on it or the first store is 0(default store)
     * If the first store is 0 then it returns the result of count($stores) > 1 to see if there are more then 1 store inside the magento installation
     * @return bool
     */
    protected function isPageUsedInMultiStores() {
        try {
            $this->page = $this->pageRepository->getById($this->getPageId());

            if (count($this->page->getStoreId()) > 1 || $this->page->getStoreId(0) === '0') {
                $stores = $this->_storeManager->getStores();
                return count($stores) > 1;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return the current param page_id
     * @return mixed
     */
    protected function getPageId() {
        return $this->getRequest()->getParam('page_id');
    }
}
