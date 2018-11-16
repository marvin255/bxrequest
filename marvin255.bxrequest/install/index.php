<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

/**
 * @inheritdoc
 */
class marvin255_bxrequest extends CModule
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'marvin255.bxrequest';
        $this->MODULE_NAME = Loc::getMessage('BX_REQUEST_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BX_REQUEST_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('BX_REQUEST_MODULE_PARTNER_NAME');
    }

    /**
     * @inheritdoc
     */
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function doUninstall()
    {
        ModuleManager::unregisterModule($this->MODULE_ID);
    }
}
