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
namespace Bss\Megamenu\Controller\Adminhtml\Item;

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
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Bss\Megamenu\Model\MenuItemsFactory $modelMenuFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Bss\Megamenu\Model\MenuItemsFactory $modelMenuFactory
        ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelMenuFactory = $modelMenuFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$params = $this->getRequest()->getParams();
    	$menu = $this->modelMenuFactory->create();

        $resultEcho = $this->resultJsonFactory->create();

        if($params['type'] == 'load') {

            $id = explode('_', $params['node_id']);
            $menu = $menu->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('menu_id', $id[1])
            ->addFieldToFilter('store_id', $params['store_id'])
            ->getLastItem();

            $result = [];
            
            if(!empty($menu->getId())) {
                $result = $menu->getData();
                $result['content'] = unserialize($menu->getContent());
                $result['empty'] = false;
                $result['mega_menu_id'] = $id[1];
            }else {
                $result['empty'] = true;
                $result['mega_menu_id'] = $id[1];
            }

            return $resultEcho->setData($result);

        } elseif($params['type'] == 'save') {

            $menu = $menu->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('menu_id', $params['menu_id'])
            ->addFieldToFilter('store_id', $params['store_id'])
            ->getLastItem();

            unset($params['type']);
            unset($params['form_key']);
            if(empty($params['item_id'])) {
                $menu->setMenuId($params['menu_id'])
                ->setStatus($params['megamenu_enable'])
                ->setType($params['megamenu_type'])
                ->setLabel($params['megamenu_label_type'])
                ->setContent(serialize($params))
                ->setBlockTop($params['megamenu_static_block_top'])
                ->setBlockLeft($params['megamenu_static_block_left'])
                ->setBlockRight($params['megamenu_static_block_right'])
                ->setBlockBottom($params['megamenu_static_block_bottom'])
                ->setBlockContent($params['megamenu_content_block'])
                ->setUrlType($params['megamenu_menu_url_type'])
                ->setCustomLink($params['custom_link'])
                ->setCategoryId($params['megamenu_category_link'])
                ->setStoreId($params['store_id'])
                ->save();
                
            }else {
                $menu->load($params['item_id'])
                ->setMenuId($params['menu_id'])
                ->setStatus($params['megamenu_enable'])
                ->setType($params['megamenu_type'])
                ->setLabel($params['megamenu_label_type'])
                ->setContent(serialize($params))
                ->setBlockTop($params['megamenu_static_block_top'])
                ->setBlockLeft($params['megamenu_static_block_left'])
                ->setBlockRight($params['megamenu_static_block_right'])
                ->setBlockBottom($params['megamenu_static_block_bottom'])
                ->setBlockContent($params['megamenu_content_block'])
                ->setUrlType($params['megamenu_menu_url_type'])
                ->setUrlType($params['megamenu_menu_url_type'])
                ->setCustomLink($params['custom_link'])
                ->setCategoryId($params['megamenu_category_link'])
                ->setStoreId($params['store_id'])
                ->save();
            }

            $result = $menu->getData();
            $result['content'] = unserialize($menu->getContent());
            return $resultEcho->setData($result);

        }
    }
}
