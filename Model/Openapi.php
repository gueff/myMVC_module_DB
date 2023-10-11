<?php

namespace DB\Model;


use Symfony\Component\Yaml\Yaml;

class Openapi
{
    /**
     * builds an openapi.yaml "DTTables.yaml" in the DataType folder based on data type classes of the DB tables
     * @param \DB\Model\DbInit|null $oDB
     * @param                       $sDtClassPrefix
     * @param                       $sOpenApiVersion
     * @return string /absolute/path/to/file.yaml | empty=fail
     * @throws \ReflectionException
     */
    public static function createDTYamlOnDTClasses(\DB\Model\DbInit $oDB = null, $sDtClassPrefix = 'DT', $sOpenApiVersion = '3.0.1')
    {
        if (null === $oDB)
        {
            return '';
        }

        (true === empty($sDtClassPrefix)) ? $sDtClassPrefix = 'DT' : false;
        (true === empty($sOpenApiVersion)) ? $sOpenApiVersion = '3.0.1' : false;

        $sDTFolderPre = '\\' . Config::get_MVC_MODULE_CURRENT_NAME() . '\\' . basename(\MVC\Config::get_MVC_MODULE_CURRENT_DATATYPE_DIR());
        $sYamlFile = \MVC\Config::get_MVC_MODULE_CURRENT_DATATYPE_DIR() . '/DTTables.yaml';
        $aClassVar = get_class_vars(get_class($oDB));
        $aTmp = array();
        $aTmp['components']['schemas'];

        foreach ($aClassVar as $sProperty => $mFoo)
        {
            $aFieldInfo = DB::$$sProperty->getFieldInfo();
            $sClass = $oDB->getDocCommentValueOfProperty($sProperty);
            $sDtClassName = $sDtClassPrefix . str_replace('\\', '', $sClass);
            $sDTofClass = $sDTFolderPre . '\\' . $sDtClassName;

            /** @var \DB\DataType\DB\TableDataType $oDtTmp */
            $oDtTmp = $sDTofClass::create();

            $aTmp['components']['schemas'][$sDtClassName] = array();
            $aTmp['components']['schemas'][$sDtClassName]['type'] = 'object';
            $aTmp['components']['schemas'][$sDtClassName]['properties'] = array();

            foreach ($oDtTmp->getPropertyArray() as $sKey => $mValue)
            {
                $aTmp['components']['schemas'][$sDtClassName]['properties'][$sKey]['type'] = gettype($mValue);
                $aTmp['components']['schemas'][$sDtClassName]['properties'][$sKey]['default'] = $mValue;
                $aTmp['components']['schemas'][$sDtClassName]['properties'][$sKey]['description'] = $aFieldInfo[$sKey]['Type'];
            }
        }

        $sYaml =
            trim('openapi: ' . $sOpenApiVersion
                 . "\n"
                 // array to yaml
                 . Yaml::dump($aTmp, 100, 2) /** @see https://symfony.com/doc/current/components/yaml.html */
            );

        file_put_contents(
            $sYamlFile,
            $sYaml
        );

        return $sYamlFile;
    }
}