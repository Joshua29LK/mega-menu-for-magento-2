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
var megamenu_create_node = false;

define([
    'jquery',
    'ko',
    'Bss_Megamenu/js/content',
    'bss/jstree'
], function ($, ko) {
    'use strict';
    $.widget('bss.bss_config', {
        _create: function () {

			
			var options = this.options;

			var urlSave = options.urlSave;
			var menuItemUrlLoad = options.menuItemUrlLoad;
			var menuItemUrlSave = options.menuItemUrlSave;
			var menuTree = options.menuTree;
			var getNodeUrlDelete = options.getNodeUrlDelete;
			var currentStoreId = options.currentStoreId;
			var messageCache = options.messageCache;
			$(document).ready(function() {

				$(document).on("change", "select#megamenu_menu_url_type", function() {
					var menuType = $(this).val();
					switch (menuType) {
						case '1':
							$(".admin__field.bss_custom_link").show();
							$(".admin__field.bss_category_link").hide();
							break;
						case '0':
							$(".admin__field.bss_custom_link").hide();
							$(".admin__field.bss_category_link").show();
							break;
						default:
							$(".admin__field.bss_custom_link").hide();
							$(".admin__field.bss_category_link").hide();
							break;
					}
				});

				$(document).on("change", "select#megamenu_type", function() {
					var menuType = $(this).val();
					switch (menuType) {
						case '1':
							$(".admin__field.bss_content_block").hide();
							$(".admin__field.bss_content_top").hide();
							$(".admin__field.bss_content_bottom").hide();
							$(".admin__field.bss_content_left").hide();
							$(".admin__field.bss_content_right").hide();
							break;
						case '2':
							$(".admin__field.bss_content_block").hide();
							$(".admin__field.bss_content_top").show();
							$(".admin__field.bss_content_bottom").show();
							$(".admin__field.bss_content_left").show();
							$(".admin__field.bss_content_right").show();
							break;
						default:
							$(".admin__field.bss_content_block").show();
							$(".admin__field.bss_content_top").show();
							$(".admin__field.bss_content_bottom").show();
							$(".admin__field.bss_content_left").show();
							$(".admin__field.bss_content_right").show();
							break;
					}
				});

				$('#megamenu_add').click(function() {
					var ref = $('#megamenu_tree').jstree(true),
					sel = ref.get_selected();
					if(!sel.length) { return false; }
					sel = sel[0];
					sel = ref.create_node(sel);
					if(sel) {
						ref.edit(sel);
					}
				});

				$('#megamenu_tree').jstree({
					'core' : {
						'data' : menuTree,
						"check_callback" : true,
						'multiple': false,
						"expand_selected_onload" : false,

					},
					"types" : {
						"#" : {
			      			"max_depth" : 4
			    		}
					},
					"contextmenu":{
					  	"items": function () {
					  		return {
					  			"Create": {
					  				"label": "Create",
					  				"action": function (data) {
					  					var ref = $.jstree.reference(data.reference);
					  					var sel = ref.get_selected();
					  					if(!sel.length) { return false; }
					  					sel = sel[0];
					  					sel = ref.create_node(sel);
					  					if(sel) {
					  						ref.edit(sel);
					  					}

					  				}
					  			},
					  			"Rename": {
					  				"label": "Rename",
					  				"action": function (data) {
					  					var inst = $.jstree.reference(data.reference);
					  					var obj = inst.get_node(data.reference);
					  					if(obj.id == 'root') return;
					  					inst.edit(obj);
					  				}
					  			},
					  			"Delete": {
					  				"label": "Delete",
					  				"action": function (data) {
					  					var ref = $.jstree.reference(data.reference);
					  					var sel = ref.get_selected();

					  					if(sel['0'] == 'root') return;
					  					if(!sel.length) { return false; }
					  					var r = confirm("Are you sure?");
					  					if (r == true) {
					  						var obj = ref.get_node(data.reference);
					  						$('.megamenu-loading').show();
											var node_id = obj.id;
											ref.delete_node(sel);
											$.ajax({
												type: "POST",
												dataType : 'json',
												data: {'node_id' : node_id , 'store_id': currentStoreId, 'menu' : JSON.stringify($('#megamenu_tree').data().jstree.get_json()) },
												url: getNodeUrlDelete,
												success: function(response) {
													$("#system_messages .message.message-warning").show();
													$("#system_messages .message-system-action-dropdown").hide();
													$("#system_messages .message.message-warning").html(messageCache);
													$('.megamenu-loading').hide();
													$("#megamenu_content").css({"cursor": "not-allowed", "pointer-events": "none"});
													var newMenuKO = megamenuKO('', 'Please Select Menu');
													var newMenuKO = megamenuKO('', 'Please Select Menu');
													ko.cleanNode(document.getElementById("megamenu_content"));
													ko.applyBindings(newMenuKO, document.getElementById("megamenu_content"));
													ko.cleanNode(document.getElementById("megamenu_content"));
													$('#megamenu_tree').jstree("deselect_all");
												},
												error: function() {
													alert("error");
													$('.megamenu-loading').hide();
													return false;
												}
											});
					  						
					  					}
					  				}
					  			}
					  		};
					  	}
			 		},
					"plugins" : [
					    "search",  "contextmenu",
					    "types", "wholerow"
					]
				}).on('rename_node.jstree', function(e, data) {

					if( megamenu_create_node || data.text != data.old ) {
						megamenu_create_node = false;
						var node = data.node;
						$('.megamenu-loading').show();
						$('#bss_notice').hide();

						$.ajax({
							type: "POST",
							dataType : 'json',
							data: {'parent' : node.parent, 'store_id': currentStoreId, 'node_id' : node.id, 'title' : data.text, 'menu' : JSON.stringify($('#megamenu_tree').data().jstree.get_json()) },
							url: urlSave,
							success: function(response) {
								$("#system_messages .message.message-warning").show();
								$("#system_messages .message-system-action-dropdown").hide();
								$("#system_messages .message.message-warning").html(messageCache);
								$('.megamenu-loading').hide();
							},
							error: function() {
								alert("error");
								$('.megamenu-loading').hide();
							}
						});
					}
				}).on('loaded.jstree', function(e, data) {
					$("#megamenu_content").css({"cursor": "not-allowed", "pointer-events": "none"});
					megamenuKO('', 'Please Select Menu');
					$('#megamenu_tree').jstree("deselect_all");
					
				}).on('create_node.jstree', function(e, data) {
					megamenu_create_node = true;

				}).on('delete_node.jstree', function(e, data) {
					
				}).on('select_node.jstree', function(e,data) {
					var node = data.node;
					if (node.id == 'root') {
						$("#megamenu_content").css({"cursor": "not-allowed", "pointer-events": "none"});
						var newMenuKO = megamenuKO('', 'Please Select Menu');
						ko.cleanNode(document.getElementById("megamenu_content"));
						ko.applyBindings(newMenuKO, document.getElementById("megamenu_content"));
						ko.cleanNode(document.getElementById("megamenu_content"));
						return;
					}

					if (node.parents.length > 2) {
						$(".admin__field.bss_content_block").hide();
						$(".admin__field.bss_content_top").hide();
						$(".admin__field.bss_content_bottom").hide();
						$(".admin__field.bss_content_left").hide();
						$(".admin__field.bss_content_right").hide();
						$(".admin__field.bss_megamenu_type").hide();
						$(".admin__field.bss_megamenu_width").hide();
					} else {
						$(".admin__field.bss_content_block").show();
						$(".admin__field.bss_content_top").show();
						$(".admin__field.bss_content_bottom").show();
						$(".admin__field.bss_content_left").show();
						$(".admin__field.bss_content_right").show();
						$(".admin__field.bss_megamenu_type").show();
						$(".admin__field.bss_megamenu_width").show();
					}

					$('.megamenu-loading').show();
					$.ajax({
						type: "POST",
						dataType : 'json',
						data: {'node_id' : node.id, 'store_id' : currentStoreId},
						url: menuItemUrlLoad,
						success: function(response) {

							$("#megamenu_content").css({"cursor": "auto", "pointer-events": "auto"});
							if(response.empty) {
								var newMenuKO = megamenuKO(response.mega_menu_id, node.text + ' (not save)');
							}else {
								var newMenuKO = megamenuKO(response.menu_id, node.text , response.id ,response.status , response.type , response.label, response.content.megamenu_static_block_top , response.content.megamenu_static_block_right , response.content.megamenu_static_block_bottom , response.content.megamenu_static_block_left,response.content.megamenu_width , response.content.megamenu_content_block , response.content.megamenu_menu_url_type , response.content.custom_link , response.content.megamenu_category_link);
							}

							ko.cleanNode(document.getElementById("megamenu_content"));
							ko.applyBindings(newMenuKO, document.getElementById("megamenu_content"));
							ko.cleanNode(document.getElementById("megamenu_content"));
							$('.megamenu-loading').hide();
						},
						error: function() {
							alert("error");
							$('.megamenu-loading').hide();
						}
					});
				});

				$(document).keypress(function(e) {
				    if(e.which == 13) {
				        saveConfig();
				        return false;
				    }
				});

				$('#save-menu-button').click(function() {
					saveConfig();
				});
			});

			function saveConfig() {
				var node = $('#megamenu_tree').jstree(true).get_selected('full',true)[0];

				if(typeof node === "undefined") {
					alert('Please Select Menu.');
					return;
				};

				if(node.id == 'root') {
					alert('Please Select Menu.');
					return;
				};

				var typeLink = $("select#megamenu_menu_url_type").val();
				if (typeLink == '1') {
					var customLink = $("input#custom_link").val();
					var validateUrl = validURL(customLink);
					if (!validateUrl) return false;
				}

				$('.megamenu-loading').show();
				
				$.ajax({
					type: "POST",
					dataType : 'json',
					data: $('#megamenu_content').serialize(),
					url: menuItemUrlSave,
					success: function(response) {
						
						$("#system_messages .message.message-warning").show();
						$("#system_messages .message-system-action-dropdown").hide();
						$("#system_messages .message.message-warning").html(messageCache);

						ko.cleanNode(document.getElementById("megamenu_content"));
						var newMenuKO = megamenuKO(response.menu_id, node.text , response.id ,response.status , response.type , response.label, response.content.megamenu_static_block_top , response.content.megamenu_static_block_right , response.content.megamenu_static_block_bottom , response.content.megamenu_static_block_left, response.content.megamenu_width, response.content.megamenu_content_block , response.content.megamenu_menu_url_type , response.content.custom_link ,response.content.megamenu_category_link);
						ko.applyBindings(newMenuKO, document.getElementById("megamenu_content"));
						ko.cleanNode(document.getElementById("megamenu_content"));
						$('#bss_notice').fadeIn(300);
						$('#bss_notice').html("Saved Menu Success");
						$('.megamenu-loading').hide();
						setTimeout(function(){
						    $("#bss_notice").fadeOut(300);
						}, 1500);
					},
					error: function() {
						alert("error");
						$('.megamenu-loading').hide();
					}
				});
			}

			function validURL(url){
			    if(/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url)) {
			   		return true;
				} else {
					alert("Please enter a valid URL.");
					return false;
					
				}
			}

			function megamenuKO(id,title,item_id,active,type,label,top,right,bottom,left, width, block_content, url_type , custom_link , category_link) {

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
	            self.chosenWidthType = ko.observable(width);
	            self.chosenContentBlock = ko.observable(block_content);
	            self.chosenMenuUrlType = ko.observable(url_type);
	            self.chosenCategoriesLink = ko.observable(category_link);
	            self.customLink = custom_link;
	        }
		}
	});
	
	return $.bss.bss_config;
});