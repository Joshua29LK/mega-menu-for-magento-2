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
namespace Bss\Megamenu\Block\Html;

use Magento\Framework\View\Element\Template;

class Menu extends Template
{
    /**
     * @var \Bss\Megamenu\Helper\Data
     */
    protected $helper;

    /**
     * @var \Bss\Megamenu\Model\Menu
     */
    protected $menu;

    /**
     * @var \Bss\Megamenu\Model\ResourceModel\MenuItems\Collection
     */
    protected $menuItems;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Theme\Block\Html\Topmenu
     */
    protected $topMenuDefault;

    const DEFAULT_STOREVIEW = '0';

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param \Bss\Megamenu\Helper\Data $helper
     * @param \Bss\Megamenu\Model\Menu $menu
     * @param \Bss\Megamenu\Model\ResourceModel\MenuItems\Collection $menuItems
     * @param \Magento\Theme\Block\Html\Topmenu $topMenuDefault
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\Megamenu\Helper\Data $helper,
        \Bss\Megamenu\Model\Menu $menu,
        \Bss\Megamenu\Model\ResourceModel\MenuItems\Collection $menuItems,
        \Magento\Theme\Block\Html\Topmenu $topMenuDefault,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        array $data = []
        ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->topMenuDefault = $topMenuDefault;
        $this->menu = $menu;
        $this->storeManager = $storeManager;
        $this->menuItems = $menuItems;
        $this->customerSession = $customerSession;
        $this->resource = $resource;
        $this->scopeConfig = $scopeInterface;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return \Bss\Megamenu\Helper\Data
     */
    public function getHelperData()
    {
        return $this->helper;
    }

    /**
     * @return \Magento\Theme\Block\Html\Topmenu
     */
    public function getTopMenuDefault()
    {
        return $this->topMenuDefault;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHtml()
    {

        $collection = $this->menuItems->addFieldToFilter('status', 1);

        $new_arr = [];
        foreach ( $collection->getData() as $arr)
        {
            $new_arr['j1_'.$arr['menu_id']] = $arr;
        };

        $menu = json_decode($this->scopeConfig->getValue('megamenu/tree/data',\Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        if (isset($menu[0])) {
            $menu = get_object_vars($menu[0]);
        }

        if(count($menu['children']) == 0) {
            return '';
        }

        $html = $this->_getHtml($menu['children'],$new_arr);
        return $html;
    }

    /**
     * @param $menus
     * @param $collection
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getHtml($menus, $collection)
    {
        $html = '';
        $i = 1;
        foreach ($menus as $menu) {
            if(!array_key_exists($menu->id, $collection)) continue;
            $menu2 = $collection[$menu->id];


            if (isset($menu->children[0])) {
                $childrenText = 'parent';
            } else {
                $childrenText = '';
            }

            $checkIsFullWith = 0;

            if ($menu2['type'] == 2 || $menu2['type'] == 3) {
                $checkIsFullWith = 1;
            }

            $html .= '<li class="level0 dropdown ' . ($checkIsFullWith == 1 ? 'bss-megamenu-fw ' : '') . 'level-top '.$childrenText.' ui-menu-item"><a class="level-top ui-corner-all" href="'.$this->helper->getLinkUrl($menu2).'" ><span>'. $menu->text;
            if($menu2['label'] != '') {
                $html .= $this->helper->getLabelColor($menu2['label']);
            }

            $html .= '</span></a>';

            switch($menu2['type']) {
                case 1:
                    $html .= $this->_getChildHtmlDefault($menu, 0, $i, $collection);
                    break;

                case 2:
                    $html .= $this->_getChildHtmlCatagoryList($menu, $collection);
                    break;

                case 3:
                    $html .= $this->_getChildHtmlContent($menu, $collection);
                    break;
            }

            $html .= '</li>';
            $i++;
        }
        return $html;
    }

    /**
     * @param $menu
     * @param $level
     * @param $nav
     * @param $collection
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getChildHtmlDefault($menu, $level, $nav, $collection)
    {
        $html = '';
        if(count($menu->children) == 0 ) return $html;

        $countCollection = 0;
        foreach($menu->children as $childrens) {
            if(array_key_exists($childrens->id, $collection)) {
                $countCollection++;
            }
        }

        if ($countCollection == 0) {
            return $html;
        }

        $html .= '<ul class="dropdown-menu fullwidth level0 submenu ui-menu ui-widget ui-widget-content ui-corner-all" role="menu">';
        $i = 1;
        $level++;
        foreach($menu->children as $childrens) {
            if(!array_key_exists($childrens->id, $collection)) continue;

            $menu2 = $collection[$childrens->id];
            $html .= '<li class="dropdown-submenu level1 nav-4-1 first ui-menu-item"><a class="ui-corner-all" href="'.$this->helper->getLinkUrl($menu2).'"><span>'.$childrens->text.'</span>';
            if($menu2['label'] != '') {
                $html .= $this->helper->getLabelColor($menu2['label']);
            }
            $html .= '</a>';
            $nav_child = $nav.'-'.$i;
            if(isset($childrens->children[0])) {
                $html .= $this->_getChildHtmlDefault($childrens, $level, $nav_child, $collection);
            }
            $html .= '</li>';
            $i++;
        }
        $html .= '</ul>';
        return $html;
    }


    /**
     * @param $menu
     * @param $level
     * @param $nav
     * @param $collection
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getChildHtmlCatagoryList($menu, $collection)
    {
        $html = '';

        $countCollection = 0;
        foreach($menu->children as $childrens) {
            if(array_key_exists($childrens->id, $collection)) {
                $countCollection++;
            }
        }

        $menu2 = $collection[$menu->id];

        if ($countCollection == 0 && $menu2['block_top'] == '' && $menu2['block_left'] == '' && $menu2['block_bottom'] == '' && $menu2['block_right'] == '') {
            return $html;
        }

        $html .= '<ul class="dropdown-menu fullwidth"><li class="bss-megamenu-content withdesc">';
        

        if($menu2['block_top'] != '') {
            $html .= '<div class="row">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_top'])->toHtml();
            $html .= '</div><hr>';
        }

        $html .= '<div class="row">';

        $size = $this->helper->checkSize($menu2);
        
        if($menu2['block_left'] != '') {
            $html .= '<div class="col-sm-'.$size.'">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_left'])->toHtml();
            $html .= '</div>';
        }

        $html .= '<div class="col-sm-'.$size.'">';

        $html = $this->_getChildHtmlCatagoryListSecond($menu, $collection, $html, $menu2, $size);

        if($menu2['block_bottom'] != '') {
            $html .= '<hr><div class="row">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_bottom'])->toHtml();
            $html .= '</div>';
        }

        $html .= '</li></ul>';
        return $html;
    }

    /**
     * @param $menu
     * @param $collection
     * @param $html
     * @param $menu2
     * @param $size
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getChildHtmlCatagoryListSecond($menu, $collection, $html, $menu2, $size)
    {
        foreach($menu->children as $childrens) {
            if(!array_key_exists($childrens->id, $collection)) continue;

            $html .= '<div class="col-sm-6"><h3 class="title"><a href="'.$this->helper->getLinkUrl($collection[$childrens->id]).'">'.$childrens->text;
            if($collection[$childrens->id]['label'] != '') {
                $html .= $this->helper->getLabelColor($collection[$childrens->id]['label']);
            }
            $html .= '</a></h3>';

            if(isset($childrens->children[0])) {
                $html .= '<ul>';
                foreach($childrens->children as $child) {
                    if(!array_key_exists($child->id, $collection)) continue;
                    $html .= '<li><a href="'.$this->helper->getLinkUrl($collection[$child->id]).'"><span>'.$child->text.'</span>';
                    if($collection[$child->id]['label'] != '') {
                        $html .= $this->helper->getLabelColor($collection[$child->id]['label']);
                    }
                    $html .= '</a></li>';
                }
                $html .= '</ul>';
            }

            $html .= '</div>';
        }
        $html .= '</div>';

        if($menu2['block_right'] != '') {
            $html .= '<div class="col-sm-'.$size.'">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_right'])->toHtml();
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }


    /**
     * @param $menu
     * @param $collection
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getChildHtmlContent($menu, $collection)
    {
        $html = '';

        $menu2 = $collection[$menu->id];

        if ($menu2['block_top'] == '' && $menu2['block_left'] == '' && $menu2['block_bottom'] == '' && $menu2['block_right'] == '') {
            return $html;
        }

        
        $html .= '<ul class="dropdown-menu fullwidth"><li class="bss-megamenu-content">';

        if($menu2['block_top'] != '') {
            $html .= '<div class="row">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_top'])->toHtml();
            $html .= '</div><hr>';
        }

        $html .= '<div class="row">';

        $size = $this->helper->checkSize($menu2);
        
        if($menu2['block_left'] != '') {
            $html .= '<div class="col-sm-'.$size.'">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_left'])->toHtml();
            $html .= '</div>';
        }

        $html .= '<div class="col-sm-'.$size.'">';
        $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_content'])->toHtml();
        $html .= '</div>';

        if($menu2['block_right'] != '') {
            $html .= '<div class="col-sm-'.$size.'">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_right'])->toHtml();
            $html .= '</div>';
        }
        
        $html .= '</div>';

        if($menu2['block_bottom'] != '') {
            $html .= '<hr><div class="row">';
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($menu2['block_bottom'])->toHtml();
            $html .= '</div>';
        }
        
        $html .= '</li></ul>';
        return $html;
    }

}
