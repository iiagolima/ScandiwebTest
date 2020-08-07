<?php
namespace ScandiwebTest\MultistoreMetatag\Block;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Helper\Page;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

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
     * @return string
     */
    protected function _toHtml()
    {
        if($this->isPageCms() && $this->isPageUsedInMultiStores()) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getStoreLanguage() {
        return $this->localeResolver->getLocale();
    }

    /**
     * @return string|null
     */
    public function getPageUrl() {
        return $this->_urlBuilder->getUrl(null, ['_direct' => $this->page->getIdentifier()]);
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
     * @return bool
     */
    protected function isPageUsedInMultiStores() {
        try {
            $this->page = $this->pageRepository->getById($this->getPageId());

            if (count($this->page->getStoreId()) > 1) {
                return true;
            } else {
                $firstStore = $this->page->getStoreId(0);
                if ($firstStore === '0') {
                    $stores = $this->_storeManager->getStores();
                    return count($stores) > 1;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return mixed
     */
    protected function getPageId() {
        return $this->getRequest()->getParam('page_id');
    }
}
