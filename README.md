## Overview

- [1. Requirements](#1)
- [2. Repository](#2)
- [3. Creation](#3)
	- [3.1. Examples](#3-1)
	- [3.2. Explained](#3-2)
- [4. Events](#4)

---


### 1. Requirements <a name="1"></a>

- Linux
- php 7
    - PDO
- [myMVC >= 1.3.0](https://github.com/gueff/myMVC/releases/tag/1.3.0)
        

---


## 2. Repository <a name="2"></a>


- https://github.com/gueff/myMVC_module_DB



## 3. Creation <a name="2"></a>

### 3.1. Example <a name="3-1"></a>

_PHP Class `Model/DB/TableFoo.php`_  
as a Representation of the DB Table.

Create the Folder `Model/DB`.
There, create the file `TableFoo.php` as follows.

_Most simple_  
~~~php
<?php

namespace Foo\Model\DB; // myMVC module "Foo" > Model/DB/
use DB\DataType\DB\Foreign;
use MVC\DataType\DTArrayObject;
use MVC\DataType\DTKeyValue;

class TableFoo extends \DB\Model\Db
{	
    /**
     * @var array 
     */
    protected $aFields = array(
        'hash'                  => "varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT  'aus: recipientEmail,reason,+SALT'",
        'dateTimeDelivered'     => 'datetime',
    );

    /**
     * TableFoo constructor.
     * @param array $aDbConfig
     */
    public function __construct(array $aDbConfig = array())
    {
        // basic creation of the table
        parent::__construct(
            $this->aFields, 
            $aDbConfig
        );

	$this->setForeignKey(
		Foreign::create()
			->set_sForeignKey('id_LCPModelTableUserGroupRel')
			->set_sReferenceTable('LCPModelTableUserGroupRel')
			->set_sOnDelete(Foreign::DELETE_CASCADE)
	);

	$this->setForeignKey(
		Foreign::create()
			->set_sForeignKey('id_LCPModelTableLCP')
			->set_sReferenceTable('LCPModelTableLCP')
			->set_sOnDelete(Foreign::DELETE_CASCADE)
	);
        
        // sync Table Fields according to $aFields 
        $this->synchronizeFields();

        // creating a DataType Class according to the table
        $this->generateDataType();
    }
}
~~~

- creates the Table "TableFoo"
- Table has fields "hash", "dateTimeDelivered"
- generates a DataType Class "DataType/DTTableFoo.php" in the Module where the TableFoo resides
- The foreign keys `id_LCPModelTableUserGroupRel` and `id_LCPModelTableLCP` are added by method `setForeignKey()`
- generates a DataType Class "DataType/DTFooModelDBTableFoo.php" in the Module where the TableFoo resides


---

_Usage_  
~~~php
$oTableFoo = new TableFoo($aDbConfig);
~~~

_Db Config_  
~~~php
$aDbConfig = array(
    'db' => array(
        'host' => 'localhost',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ),
    'caching' => array(
        'enabled' => true,
        'lifetime' => '7200'
    ),
    'logging' => array(
        'log_output' => 'FILE',
    
         // consider to turn it on for develop and test environments only
        'general_log' => 'ON',
    
         // 1) make sure write access is given to the folder
         // as long as the db user is going to write and not the webserver user
         // 2) consider a logrotate mechanism for this logfile as it may grow quickly
        'general_log_file' => '/tmp/db.log'
    )
);
~~~

### 3.2. Explained <a name="3-2"></a

_Fields_  
~~~php
/**
 * @var array
 */
protected $aFields = array(
	'name'          => 'varchar(255)    COLLATE utf8mb4_bin NOT NULL    COMMENT "Company"',
	'ip'            => 'varchar(19)     COLLATE utf8mb4_bin NOT NULL    COMMENT "IP Adresse"',
	'success'       => 'int(1)											COMMENT ""',
	'datetimeStart'	=> 'datetime										COMMENT "Phase Beginn"',
	'datetimeEnd'	=> 'datetime										COMMENT "Phase Ende"',
	'kwStart'       => 'int(2)								NOT NULL	COMMENT "KW Start"',
	'kwEnd'         => 'int(2)      						NOT NULL 	COMMENT "KW Ende"',
    'description'   => 'text            COLLATE utf8mb4_bin	NOT NULL    COMMENT "Beschreibung"'
);
~~~


_`count`_  
~~~php
// Amount of all Datasets
$iAmount = $this->count();

// Amount of specific Datasets
$iAmount = $this->count(
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
          ->set_sKey('stampChange')
          ->set_mOptional1('=')
          ->set_sValue('2021-06-19')
    )  
);
~~~

_`get`_  
~~~php
// get all Datasets
$aDataType = $this->get();

// get first 30 Datasets (LIMIT 0,30)
$aDataType = $this->get(
  0,
  null,
  DTArrayObject::create()
    ->add_aKeyValue(
      DTKeyValue::create()->set_sValue('LIMIT 0,30')
    )   
);

// get Dataset with id:1
$aDataType = $this->get(1);

// get specific Datasets
$aDataType = $this->get(
  0,
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
          ->set_sKey('stampChange')
          ->set_mOptional1('=')
          ->set_sValue('2021-06-19')
    ) 
);

// get Datasets with sort order
$aDataType = $this->get(
  0,
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
          ->set_sKey('stampChange')
          ->set_mOptional1('=')
          ->set_sValue('2021-06-19')
    ),
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
            ->set_sValue('ORDER BY id DESC')
    )
);
~~~

_`retrieve`_  
~~~php
// get specific Datasets
$aDataType = $this->retrieve(
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
          ->set_sKey('stampChange')
          ->set_mOptional1('=')
          ->set_sValue('2021-06-19')
    ); 
);

// get Datasets with sort order
$aDataType = $this->retrieve(
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
          ->set_sKey('stampChange')
          ->set_mOptional1('=')
          ->set_sValue('2021-06-19')
    ),
  DTArrayObject::create()
    ->add_aKeyValue(
        DTKeyValue::create()
            ->set_sValue('ORDER BY id DESC')
    )
);
~~~


_`SQL`_    
~~~php
/**
	* @param DTLCPModelTableLCP $oDTLCPModelTableLCP
	* @return array
	*/
public function getUrlAndClick(DTLCPModelTableLCP $oDTLCPModelTableLCP)
{
	$sSql = "
		SELECT CLICK.*, URL.urlOriginal, URL.urlMod
		FROM `LCPModelTableClick` AS CLICK
		RIGHT JOIN `LCPModelTableUrl` AS URL ON CLICK.id_LCPModelTableUrl = URL.id
		WHERE 1
		AND URL.id_LCPModelTableLCP = " . (int) $oDTLCPModelTableLCP->get_id();

	$aResult = $this->oDbPDO->fetchAll($sSql);

	return $aResult;
}
~~~

_`create` (INSERT)_    
therefore an object of its related Datatype must be instaciated and given to the method `create`. Here e.g. with Datatype "DTMandosModelDBTableUser" to TableClass "modules/Mandos/Model/DB/TableUser":  
~~~php
// inside TableClass:
$this->create(DTMandosModelDBTableUser::create()
    ->set_id(1)
    ->set_id_Status(1)
    ->set_id_Group(1)
    ->set_id_UserAdmin(1)
    ->set_gender('male')
    ->set_name('')
    ->set_pass('$2y$10$a8znPSGLJVKqKHbKi9u8ee7Vc67CeRAHAK2cd1MQX3etm7fdOkRH6') # test
    ->set_firstname('Portal')
    ->set_lastname('Admin')
    ->set_email('portal.admin@mediafinanz.de')
    ->set_timezone('Europe/Berlin')
    ->set_description('darf ClientAdmin anlegen')
    ->set_stampChange($sNow)
    ->set_stampCreate($sNow));
~~~


## 4. Events <a name="4"></a>

~~~
db.model.db.setSqlLoggingState.exception
db.model.db.setForeignKey.exception
db.model.db.checkIfTableExists.exception
db.model.db.createTable.exception
db.model.db.synchronizeFields.exception
db.model.db.synchronizeFields.delete.exception
db.model.db.synchronizeFields.insert.exception
db.model.db.synchronizeFields.update.exception
db.model.db.create.sql
db.model.db.create.exception
db.model.db.retrieve.sql
db.model.db.retrieve.exception
db.model.db.count.sql
db.model.db.count.exception
db.model.db.update.sql
db.model.db.update.exception
db.model.db.delete.sql
db.model.db.delete.exception
~~~
