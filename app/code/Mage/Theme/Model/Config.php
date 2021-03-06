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
 * @package     Mage_Theme
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Theme Config model
 */
class Mage_Theme_Model_Config
{
    /**
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Mage_Core_Model_Config_Data
     */
    protected $_configData;

    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application event manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_configCache;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_layoutCache;

    /**
     * @param Mage_Core_Model_Config_Data $configData
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Magento_Cache_FrontendInterface $configCache
     * @param Magento_Cache_FrontendInterface $layoutCache
     */
    public function __construct(
        Mage_Core_Model_Config_Data $configData,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_Event_Manager $eventManager,
        Magento_Cache_FrontendInterface $configCache,
        Magento_Cache_FrontendInterface $layoutCache
    ) {
        $this->_configData   = $configData;
        $this->_configWriter = $configWriter;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_configCache  = $configCache;
        $this->_layoutCache  = $layoutCache;
    }

    /**
     * Assign theme to the stores
     *
     * @param Mage_Core_Model_Theme $theme
     * @param array $stores
     * @param string $scope
     * @return $this
     */
    public function assignToStore($theme, array $stores = array(), $scope = Mage_Core_Model_Config::SCOPE_STORES)
    {
        $isReassigned = false;

        $this->_unassignThemeFromStores(
            $theme->getId(), $stores, $scope, $isReassigned
        );

        if ($this->_storeManager->isSingleStoreMode()) {
            $this->_assignThemeToDefaultScope($theme->getId(), $isReassigned);
        } else {
            $this->_assignThemeToStores($theme->getId(), $stores, $scope, $isReassigned);
        }

        if ($isReassigned) {
            $this->_configCache->clean();
            $this->_layoutCache->clean();
        }

        $this->_eventManager->dispatch('assign_theme_to_stores_after',
            array(
                'stores' => $stores,
                'scope'  => $scope,
                'theme'  => $theme,
            )
        );

        return $this;
    }

    /**
     * Get assigned scopes collection of a theme
     *
     * @param string $scope
     * @param string $configPath
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getAssignedScopesCollection($scope, $configPath)
    {
        return $this->_configData->getCollection()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('path', $configPath);
    }

    /**
     * Unassign given theme from stores that were unchecked
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return $this
     */
    protected function _unassignThemeFromStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
        /** @var $config Mage_Core_Model_Config_Data */
        foreach ($this->_getAssignedScopesCollection($scope, $configPath) as $config) {
            if ($config->getValue() == $themeId && !in_array($config->getScopeId(), $stores)) {
                $this->_configWriter->delete($configPath, $scope, $config->getScopeId());
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * Assign given theme to stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return $this
     */
    protected function _assignThemeToStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
        if (count($stores) > 0) {
            foreach ($stores as $storeId) {
                $this->_configWriter->save($configPath, $themeId, $scope, $storeId);
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * Assign theme to default scope
     *
     * @param int $themeId
     * @param bool $isReassigned
     * @return $this
     */
    protected function _assignThemeToDefaultScope($themeId, &$isReassigned)
    {
        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
        $this->_configWriter->save($configPath, $themeId, Mage_Core_Model_Config::SCOPE_DEFAULT);
        $isReassigned = true;
        return $this;
    }
}
