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
namespace Bss\Megamenu\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
 
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
 
        $tableName = $installer->getTable('bss_megamenu_items');

        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {

            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'menu_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Menu ID'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Status'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Type'
                )
                ->addColumn(
                    'label',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'block_top',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'block_left',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'block_right',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'block_bottom',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'width',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Label'
                )
                ->addColumn(
                    'block_content',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'url_type',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Label'
                )
                ->addColumn(
                    'custom_link',
                    Table::TYPE_TEXT,
                    200,
                    ['nullable' => false, 'default' => ''],
                    'Label'
                )
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Label'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => NULL],
                    'Label'
                )
                ->addColumn(
                    'content',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Content'
                )
                ->addIndex(
                    $installer->getIdxName('bss_megamenu_items', ['id']),
                    ['id']
                )
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}