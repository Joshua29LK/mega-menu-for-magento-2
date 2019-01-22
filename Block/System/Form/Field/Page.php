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
namespace Bss\Megamenu\Block\System\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class Page
 */
class Page extends Select
{

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Page constructor.
     * @param Context $context
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $factory
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $factory,
        array $data = []
        )
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $factory;
    }
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $page = $this->collectionFactory->create()
            ->loadData()
            ->toOptionIdArray();
            $this->setOptions($page);
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     * 
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
