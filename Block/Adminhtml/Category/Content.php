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

class Content extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $blockColFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;

    /**
     * Content constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $urlBuilder
     * @param \Bss\Megamenu\Model\MenuFactory $modelMenuFactory
     * @param \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Bss\Megamenu\Model\MenuFactory $modelMenuFactory,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->resource = $resource;
        $this->blockColFactory = $blockColFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryFlatConfig = $categoryFlatState;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getBlockCollection()
    {
        $blocks = $this->blockColFactory->create();

        return json_encode($blocks->getData());
    }

    /**
     * @return string
     */
    public function getCategoryCollection() {
        $storeId = $this->getRequest()->getParam('store');

        if ($storeId == null) {
            $storeId = 0;
        }
        $category = $this->categoryFactory->create();
        $rootCat = $this->_storeManager->getStore($storeId)->getRootCategoryId();
        $category->load($rootCat);
        $data = $this->getChildCategories($category);
        return json_encode($data);
    }

    /**
     * @param $category
     * @param array $data
     * @param int $level
     * @return array
     */
    public function getChildCategories($category, $data = [], $level = 0)
    {
        if($level != 0) {
            $data[] = [ 'id' => $category->getId() , 'title' => str_repeat('-', $level - 1) . ' ' . $category->getName()];
        }
        
        if ($category->hasChildren()) {
            $childCategories = $category->getChildrenCategories();
            $level++;
            foreach ($childCategories as $childCategory) {
                $data = $this->getChildCategories($childCategory, $data, $level);
            }
        }
        return $data;
    }
}
