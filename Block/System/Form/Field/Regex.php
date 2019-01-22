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

use Magento\Framework\DataObject;

class Regex extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var
     */
	protected $pageRenderer;

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function getPageRenderer()
	{
		if (!$this->pageRenderer) {
			$this->pageRenderer = $this->getLayout()->createBlock(
				Page::class,
				'',
				['data' => ['is_render_to_js_template' => true]]
				);
		}
		return $this->pageRenderer;
	}

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _prepareToRender()
	{
		$this->addColumn(
			'megamenu_page',
			[
			'label'     => __('ID'),
			'renderer'  => $this->getPageRenderer(),
			]
			);
		$this->_addAfter = false;
		$this->_addButtonLabel = __('Add');
	}

    /**
     * @param DataObject $row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
	protected function _prepareArrayRow(DataObject $row)
	{
		$page = $row->getMegamenuPage();
		$options = [];
		if ($page) {
			$options['option_' . $this->getPageRenderer()->calcOptionHash($page)]
			= 'selected="selected"';
		}
		$row->setData('option_extra_attrs', $options);
	}

}