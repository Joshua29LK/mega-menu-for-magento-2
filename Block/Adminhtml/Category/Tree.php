<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_MegaMenu
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Megamenu\Block\Adminhtml\Category;

class Tree extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Bss\Megamenu\Model\MenuFactory
     */
    protected $modelMenuFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Bss\Megamenu\Model\ConfigFactory
     */
    protected $configFactory;

    /**
     * Tree constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Bss\Megamenu\Model\MenuFactory $modelMenuFactory
     * @param \Bss\Megamenu\Model\ConfigFactory $configFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Bss\Megamenu\Model\MenuFactory $modelMenuFactory,
        \Bss\Megamenu\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
        ) {
        $this->configFactory = $configFactory;
        $this->urlBuilder = $urlBuilder;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->resource = $resource;
        $this->scopeConfig = $scopeInterface;
        parent::__construct($context);
    }

    /**
     * @return mixed|string
     */
    public function menuTree()
    {
        $storeId = $this->getRequest()->getParam('store');

        if ($storeId == null) {
            $storeId = 0;
        }
        $configFactory = $this->configFactory->create();
        $configCollection = $configFactory->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('path', 'megamenu/tree/data')
            ->addFieldToFilter('scope_id',  $storeId)->getLastItem();
        $menu = $configCollection->getData('value');
        
        if($menu == '' || $menu == 'false') {
            $menu = '[{ "text" : "Root Menu", "id" : "root"}]';
        }

        return $menu;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        $cacheTypes = 'Page Cache';
        $message = __('One or more of the Cache Types are invalidated: %1. ', $cacheTypes) . ' ';
        $url = $this->urlBuilder->getUrl('adminhtml/cache');
        $message .= __("Please go to <a href='%1'>Cache Management</a> and refresh cache types.", $url);
        return $message;
    }

    /**
     * @param $type
     * @return string
     */
    public function getNodeUrl($type)
    {
        return $this->urlBuilder->getUrl('megamenu/category/edit', $paramsHere = ['type' => $type]);
    }

    /**
     * @param $type
     * @return string
     */
    public function getMenuItemUrl($type)
    {
        return $this->urlBuilder->getUrl('megamenu/item/edit', $paramsHere = ['type' => $type]);
    }

}
