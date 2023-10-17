<?php

namespace DB\Model;

use MVC\Config;
use MVC\Debug;
use MVC\Error;
use MVC\MVCTrait\TraitDataType;

class DbInit
{
    use TraitDataType;

    /**
     * @var null
     */
    protected static $_oInstance = null;

    /**
     * @var \DB\Model\DbPDO
     */
    public static $oPDO;

    /**
     * Constructor
     */
    protected function __construct(array $aConfig = array())
    {
        // try default fallback config; assuming it is called 'DB'
        (true === empty($aConfig)) ? $aConfig = self::getConfig() : false;

        \Cachix::init(Config::get_MVC_CACHE_CONFIG());
        $aClassVar = get_class_vars(get_class($this));

        $oDbPDO = new DbPDO($aConfig);
        self::$oPDO = $oDbPDO;

        foreach ($aClassVar as $sProperty => $mFoo)
        {
            // skip
            if ('_oInstance' === $sProperty)
            {
                continue;
            }

            $sClass = $this->getDocCommentValueOfProperty($sProperty);
            $oReflectionClass = new \ReflectionClass(get_class($this));
            $oReflectionClass->setStaticPropertyValue(
                $sProperty,
                new $sClass($aConfig)
            );
        }
    }

    /**
     * @param string $sModuleConfigKey
     * @return array
     * @throws \ReflectionException
     */
    protected static function getConfig(string $sModuleConfigKey = 'DB')
    {
        // try default fallback config; assuming it is called 'DB'
        // DB module config key
        $aConfig = Config::MODULE($sModuleConfigKey);

        // no DB module config found
        if (true === empty($aConfig))
        {
            $sMessage = 'Module Config `' . $sModuleConfigKey . '` not found. Abort. - ' . error_reporting();
            Error::error($sMessage);
            Debug::stop(
                $sMessage,
                (0 === error_reporting() ? false : true), # suppress info on 0
                (0 === error_reporting() ? false : true)  # suppress info on 0
            );
        }

        return $aConfig;
    }
}