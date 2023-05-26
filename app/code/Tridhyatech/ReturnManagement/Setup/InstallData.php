<?php
 
namespace Tridhyatech\ReturnManagement\Setup;
 
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
class InstallData implements InstallDataInterface
{
 
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        $tableName = $setup->getTable('tt_rma_reason');
        //Check for the existence of the table
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                [
                    'title' => 'Wrong Product Description',
                    'position' => 1,
                    'shipping_payer' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Wrong Product Delivered',
                    'position' => 2,
                    'shipping_payer' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Wrong Product Ordered',
                    'position' => 3,
                    'shipping_payer' => 1,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => "Product Did Not Meet Customer's Expectation",
                    'position' => 4,
                    'shipping_payer' => 1,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'No Longer Need/Wanted',
                    'position' => 5,
                    'shipping_payer' => 1,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Defective/Does Not Work Properly',
                    'position' => 6,
                    'shipping_payer' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Damaged During Shipping',
                    'position' => 7,
                    'shipping_payer' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Late Delivery of Items',
                    'position' => 8,
                    'shipping_payer' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($data as $item) {
                //Insert data
                $setup->getConnection()->insert($tableName, $item);
            }
        }
        $tableName = $setup->getTable('tt_rma_condition');
        //Check for the existence of the table
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                [
                    'title' => 'Unopened',
                    'position' => 1,
                    'status' => 1,
                ],
                [
                    'title' => 'Opened',
                    'position' => 2,
                    'status' => 1,
                ],
                [
                    'title' => 'Damaged',
                    'position' => 3,
                    'status' => 1,
                ]
            ];
            foreach ($data as $item) {
                //Insert data
                $setup->getConnection()->insert($tableName, $item);
            }
        }
        $tableName = $setup->getTable('tt_rma_resolution');
        //Check for the existence of the table
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                [
                    'title' => 'Exchange',
                    'position' => 1,
                    'status' => 1,
                ],
                [
                    'title' => 'Return',
                    'position' => 2,
                    'status' => 1,
                ],
                [
                    'title' => 'Repair',
                    'position' => 3,
                    'status' => 1,
                ],
                [
                    'title' => 'Store Credit',
                    'position' => 4,
                    'status' => 1,
                ]
            ];
            foreach ($data as $item) {
                //Insert data
                $setup->getConnection()->insert($tableName, $item);
            }
        }
        $tableName = $setup->getTable('tt_rma_return_rule');
        //Check for the existence of the table
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                [
                    'rule_status' => 1,
                    'rule_name' => 'Woman Top',
                    'conditions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product","attribute":"category_ids","operator":"==","value":"20,21","is_value_processed":false,"attribute_scope":""}]}',
                    'actions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
                    'created_date' => date('Y-m-d H:i:s'),
                    'priority' => 1,
                    'return_period' =>  10,
                    'repair_period' =>  15,         
                    'exchange_period' =>  7,
                    'default_resolution_period' =>  10,
                ],
                [
                    'rule_status' => 1,
                    'rule_name' => 'Fitness Equipment',
                    'conditions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product","attribute":"category_ids","operator":"==","value":"3,5","is_value_processed":false,"attribute_scope":""}]}',
                    'actions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
                    'created_date' => date('Y-m-d H:i:s'),
                    'priority' => 2,
                    'return_period' =>  10,
                    'repair_period' =>  15,         
                    'exchange_period' =>  7,
                    'default_resolution_period' =>  10,
                ],
                [
                    'rule_status' => 1,
                    'rule_name' => 'Watches',
                    'conditions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product","attribute":"category_ids","operator":"==","value":"3,6","is_value_processed":false,"attribute_scope":""}]}',
                    'actions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
                    'created_date' => date('Y-m-d H:i:s'),
                    'priority' => 3,
                    'return_period' =>  10,
                    'repair_period' =>  15,         
                    'exchange_period' =>  7,
                    'default_resolution_period' =>  10,
                ],
                [
                    'rule_status' => 1,
                    'rule_name' => 'Non-refundable items',
                    'conditions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":"1","is_value_processed":null,"aggregator":"all","conditions":[{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product","attribute":"category_ids","operator":"!=","value":"20,21,3,5,6","is_value_processed":false,"attribute_scope":""}]}',
                    'actions_serialized' => '{"type":"Magento\\SalesRule\\Model\\Rule\\Condition\\Product\\Combine","attribute":null,"operator":null,"value":true,"is_value_processed":null,"aggregator":"all"}',
                    'created_date' => date('Y-m-d H:i:s'),
                    'priority' => 4,
                    'return_period' =>  10,
                    'repair_period' =>  15,         
                    'exchange_period' =>  7,
                    'default_resolution_period' =>  10,
                ]
            ];
            foreach ($data as $item) {
                //Insert data
                $setup->getConnection()->insert($tableName, $item);
            }
        }
        $setup->endSetup();
    }
}