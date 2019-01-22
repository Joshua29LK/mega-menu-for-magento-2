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
namespace Bss\Megamenu\Controller\Adminhtml\Category;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * @var \Bss\Megamenu\Model\MenuItemsFactory
     */
    protected $modelMenuFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $config;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Bss\Megamenu\Model\MenuItemsFactory $modelMenuFactory
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\Megamenu\Model\MenuItemsFactory $modelMenuFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->modelMenuFactory = $modelMenuFactory;
        $this->config = $config;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultEcho = $this->resultJsonFactory->create();

    	$params = $this->getRequest()->getParams();

    	$menu = $this->modelMenuFactory->create();


        $menu = $menu->getCollection()
            ->addFieldToSelect('*');
        $menuId = $menu->getData('menu_id');

        if ($menuId) {
            $menuId = $menuId[0]['menu_id'];
        } else {
            $menuId = '';
        }

        $storeId = $params['store_id'];

        if ($storeId == '') {
            $storeId = '0';
        }

        if($params['menu']) {
            $this->config->save('megamenu/tree/data', $params['menu'], ScopeInterface::SCOPE_STORES, $storeId);
        }

        $result['empty'] = true;
        $result['mega_menu_id'] = $menuId;

        if($params['type'] == 'delete') {
            $id = explode('_', $params['node_id']);
            $menuCollection = $this->modelMenuFactory->create();
            $menuCollection = $menuCollection->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('menu_id', $id[1])
            ->addFieldToFilter('store_id', $params['store_id'])
            ->getLastItem();

            $menuCollection->delete();
        }

        return $resultEcho->setData($result);
    }
}
