<?php

/**
 * @name $DBDataTypeDB
 */
namespace DB\DataType\DB;

class Field
{
	const DTHASH = 'a155eff10c6ccfbc7522f29249b802d1';

	const CHARACTER_UTF8 = "utf8";

	const COLLATE_UTF8_BIN = "utf8_bin";

	/**
	 * @var string
	 */
	protected $sName;

	/**
	 * @var int
	 */
	protected $iLength;

	/**
	 * @var bool
	 */
	protected $bIsChangeable;

	/**
	 * @var \DB\DataType\SQL\FieldTypeConcrete
	 */
	protected $oType;

	/**
	 * @var string
	 */
	protected $sCharacter;

	/**
	 * @var string
	 */
	protected $sCollate;

	/**
	 * @var bool
	 */
	protected $bNull;

	/**
	 * @var string
	 */
	protected $sComment;

	/**
	 * Field constructor.
	 * @param array $aData
	 */
	public function __construct(array $aData = array())
	{
		$this->sName = '';
		$this->iLength = 0;
		$this->bIsChangeable = true;
		$this->oType = null;
		$this->sCharacter = '';
		$this->sCollate = '';
		$this->bNull = true;
		$this->sComment = '';

		foreach ($aData as $sKey => $mValue)
		{
			$sMethod = 'set_' . $sKey;

			if (method_exists($this, $sMethod))
			{
				$this->$sMethod($mValue);
			}
		}
	}

    /**
     * @param array $aData
     * @return Field
     */
    public static function create(array $aData = array())
    {
        $oObject = new self($aData);

        return $oObject;
    }

	/**
	 * @param string $mValue 
	 * @return $this
	 */
	public function set_sName($mValue)
	{
		$this->sName = $mValue;

		return $this;
	}

	/**
	 * @param int $mValue 
	 * @return $this
	 */
	public function set_iLength($mValue)
	{
		$this->iLength = $mValue;

		return $this;
	}

	/**
	 * @param bool $mValue 
	 * @return $this
	 */
	public function set_bIsChangeable($mValue)
	{
		$this->bIsChangeable = $mValue;

		return $this;
	}

	/**
	 * @param \DB\DataType\SQL\FieldTypeConcrete $mValue 
	 * @return $this
	 */
	public function set_oType($mValue)
	{
		$this->oType = $mValue;

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 */
	public function set_sCharacter($mValue)
	{
		$this->sCharacter = $mValue;

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 */
	public function set_sCollate($mValue)
	{
		$this->sCollate = $mValue;

		return $this;
	}

	/**
	 * @param bool $mValue 
	 * @return $this
	 */
	public function set_bNull($mValue)
	{
		$this->bNull = $mValue;

		return $this;
	}

	/**
	 * @param string $mValue 
	 * @return $this
	 */
	public function set_sComment($mValue)
	{
		$this->sComment = $mValue;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_sName()
	{
		return $this->sName;
	}

	/**
	 * @return int
	 */
	public function get_iLength()
	{
		return $this->iLength;
	}

	/**
	 * @return bool
	 */
	public function get_bIsChangeable()
	{
		return $this->bIsChangeable;
	}

	/**
	 * @return \DB\DataType\SQL\FieldTypeConcrete
	 */
	public function get_oType()
	{
		return $this->oType;
	}

	/**
	 * @return string
	 */
	public function get_sCharacter()
	{
		return $this->sCharacter;
	}

	/**
	 * @return string
	 */
	public function get_sCollate()
	{
		return $this->sCollate;
	}

	/**
	 * @return bool
	 */
	public function get_bNull()
	{
		return $this->bNull;
	}

	/**
	 * @return string
	 */
	public function get_sComment()
	{
		return $this->sComment;
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sName()
	{
        return 'sName';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_iLength()
	{
        return 'iLength';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_bIsChangeable()
	{
        return 'bIsChangeable';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_oType()
	{
        return 'oType';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sCharacter()
	{
        return 'sCharacter';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sCollate()
	{
        return 'sCollate';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_bNull()
	{
        return 'bNull';
	}

	/**
	 * @return string
	 */
	public static function getPropertyName_sComment()
	{
        return 'sComment';
	}

	/**
	 * @return false|string JSON
	 */
	public function __toString()
	{
        return $this->getPropertyJson();
	}

	/**
	 * @return false|string
	 */
	public function getPropertyJson()
	{
        return json_encode($this->getPropertyArray());
	}

	/**
	 * @return array
	 */
	public function getPropertyArray()
	{
        return get_object_vars($this);
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	public function getConstantArray()
	{
		$oReflectionClass = new \ReflectionClass($this);
		$aConstant = $oReflectionClass->getConstants();

		return $aConstant;
	}

	/**
	 * @return $this
	 */
	public function flushProperties()
	{
		foreach ($this->getPropertyArray() as $sKey => $aValue)
		{
			$sMethod = 'set_' . $sKey;

			if (method_exists($this, $sMethod)) 
			{
				$this->$sMethod('');
			}
		}

		return $this;
	}

	/**
	 * @return string JSON
	 */
	public function getDataTypeConfigJSON()
	{
		return '{"name":"Field","file":"Field.php","extends":"","namespace":"DB\\\\DataType\\\\DB","constant":[{"key":"CHARACTER_UTF8","value":"\\"utf8\\"","visibility":""},{"key":"COLLATE_UTF8_BIN","value":"\\"utf8_bin\\"","visibility":""}],"property":[{"key":"sName","var":"string","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"iLength","var":"int","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"bIsChangeable","var":"bool","value":true,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"oType","var":"\\\\DB\\\\DataType\\\\SQL\\\\FieldTypeConcrete","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"sCharacter","var":"string","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"sCollate","var":"string","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"bNull","var":"bool","value":true,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true},{"key":"sComment","var":"string","value":null,"visibility":"protected","static":false,"setter":true,"getter":true,"explicitMethodForValue":false,"listProperty":true,"createStaticPropertyGetter":true,"setValueInConstructor":true}],"createHelperMethods":true}';
	}

}
