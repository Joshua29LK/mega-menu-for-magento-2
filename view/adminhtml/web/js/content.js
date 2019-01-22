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
define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Bss_Megamenu/content'
        },
        initialize: function() {

            this._super();
            var self = this;
            self.megamenuKO();

        },
        megamenuKO: function(id,title,item_id,active,type,label,top,right,bottom,left, width, block_content, url_type , custom_link , category_link) {

            self.enables = [
                {'id': 1 , 'title' : 'Yes'},
                {'id': 0 , 'title' : 'No'}
            ];

            self.widthTypes = [
                {'id': 1 , 'title' : 'Full Width'},
                {'id': 0 , 'title' : 'Classic'}
            ];

            self.menuUrlType = [
                {'id': 1 , 'title' : 'Custom Link'},
                {'id': 0 , 'title' : 'Category Link'}
            ];

            self.contentTypes = [
                { 'id': 1 , 'title' : 'Classic' },
                { 'id': 2 , 'title' : 'Category Listing' },
                { 'id': 3 , 'title' : 'Content' }
            ],

            self.labelTypes = [
                { 'id': 'new' , 'title' : 'New' },
                { 'id': 'hot' , 'title' : 'Hot' },
                { 'id': 'sale' , 'title' : 'Sale' }
            ];

            self.static_blocks = this.staticBlocks;

            self.categoriesLink = this.categoriesLink;

            self.menuId = ko.observable(id);
            self.itemId = ko.observable(item_id);
            self.menuTitle = ko.observable(title);
            self.chosenEnable = ko.observable(active);
            self.chosenContentType = ko.observable(type);
            self.chosenLabelType = ko.observable(label);
            self.chosenBlockTop = ko.observable(top);
            self.chosenBlockRight = ko.observable(right);
            self.chosenBlockBottom = ko.observable(bottom);
            self.chosenBlockLeft = ko.observable(left);
            self.chosenContentBlock = ko.observable(block_content);
            self.chosenMenuUrlType = ko.observable(url_type);
            self.chosenCategoriesLink = ko.observable(category_link);
            self.customLink = custom_link;
            self.storeId = this.storeId;
        }
    });
});
