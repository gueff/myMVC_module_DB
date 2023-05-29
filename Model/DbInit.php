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
     * Constructor
     */
    public function __construct(array $aConfig = array())
    {
        // try default fallback config; assuming it is called 'DB'
        if (true === empty($aConfig))
        {
            // DB module config key
            $sModuleConfigKey = 'DB';
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
        }

        \Cachix::init(Config::get_MVC_CACHE_CONFIG());
        $aClassVar = get_class_vars(get_class($this));

        foreach ($aClassVar as $sProperty => $mFoo)
        {
            $sClass = $this->getDocCommentValueOfProperty($sProperty);
            $oReflectionClass = new \ReflectionClass(get_class($this));
            $oReflectionClass->setStaticPropertyValue(
                $sProperty,
                new $sClass($aConfig)
            );
        }
    }
}
