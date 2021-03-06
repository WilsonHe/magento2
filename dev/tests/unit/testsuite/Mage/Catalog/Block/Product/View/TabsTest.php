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
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_Block_Product_View_TabsTest extends PHPUnit_Framework_TestCase
{
    public function testAddTab()
    {
        $tabBlock = $this->getMock('Mage_Core_Block_Template', array(), array(), '', false);
        $tabBlock->expects($this->once())
            ->method('setTemplate')
            ->with('template')
            ->will($this->returnSelf());

        $layout = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('block')
            ->will($this->returnValue($tabBlock));

        $context = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $block = new Mage_Catalog_Block_Product_View_Tabs($context);
        $block->addTab('alias', 'title', 'block', 'template', 'header');

        $expectedTabs = array(
            array('alias' => 'alias', 'title' => 'title', 'header' => 'header')
        );
        $this->assertEquals($expectedTabs, $block->getTabs());
    }
}
