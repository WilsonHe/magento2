<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order's status management block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Status extends Mage_Backend_Block_Widget_Grid_Container
{
    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_headerText = Mage::helper('Mage_Sales_Helper_Data')->__('Order Statuses');
        $this->_addButtonLabel = Mage::helper('Mage_Sales_Helper_Data')->__('Create New Status');
        $this->_addButton('assign', array(
            'label'     => Mage::helper('Mage_Sales_Helper_Data')->__('Assign Status to State'),
            'onclick'   => 'setLocation(\'' . $this->getAssignUrl() .'\')',
            'class'     => 'add',
        ));
        parent::_construct();
    }

    /**
     * Create url getter
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_status/new');
    }

    /**
     * Assign url getter
     *
     * @return string
     */
    public function getAssignUrl()
    {
        return $this->getUrl('*/sales_order_status/assign');
    }
}
