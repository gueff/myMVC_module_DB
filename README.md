
# myMVC_module_DB

<img src="https://github.com/gueff/myMVC_module_DB/actions/workflows/super-linter.yml/badge.svg">

- [1. Requirements](#1)
- [2. Repository](#2)
- [3. Creation](#3)
    - [3.1. Examples](#3-1)
    - [3.2. Explained](#3-2)
- [4. Events](#4)
    - [4.1. Logging SQL](#4-1)

---

<a id="1"></a>

## 1. Requirements

- Linux
- php >= 7.4
  - `pdo` extension
- myMVC 3.2.x
  - `git clone --branch 3.2.x https://github.com/gueff/myMVC.git myMVC_3.2.x`
  - github: <https://github.com/gueff/myMVC/tree/3.2.x>
  - Docs: <https://mymvc.ueffing.net/>

<a id="2"></a>

## 2. Repository

- <https://github.com/gueff/myMVC_module_DB>

<a id="3"></a>

## 3. Creation

<a id="3-1"></a>

### 3.1. Examples

_PHP Class_  
as a Representation of the DB Table

_Most simple_

~~~php
<?php

class TableFoo extends \DB\Model\Db
{ 
    /**
    * no need to declare field `id` - this will be always declared automatically
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
    }
}
~~~

- creates the Table `TableFoo`
  - Table has fields `hash`, `dateTimeDelivered` as declared in property `$aFields`
    - 🛈 The Table fields `id`, `stampChange` and `stampCreate` are added automatically
- generates a DataType Class `DataType/DTFooModelTableFoo.php`

---

_Creating a Table and adding a Foreign Key_

~~~php
<?php

namespace LCP\Model;

use DB\DataType\DB\Foreign;

class TableUrl extends \DB\Model\Db
{
    /**
    * @var array
    */
    protected $aField = array(
        'urlOriginal'           => "tinytext        CHARACTER SET utf8 COLLATE utf8_bin NOT NULL",
        'urlMod'                => "tinytext        CHARACTER SET utf8 COLLATE utf8_bin NOT NULL",
    );
    
    /**
    * TableUrl constructor.
    * @param array $aDbConfig
    * @throws \ReflectionException
    */
    public function __construct(array $aDbConfig = array())
    {
        parent::__construct(
            $this->aField,
            $aDbConfig
        );
        
        $this->setForeignKey(
            Foreign::create()
                ->set_sForeignKey('id_LCPModelTableLCP')
                ->set_sReferenceTable('LCPModelTableLCP')
                ->set_sOnDelete(Foreign::DELETE_CASCADE)
        );
    }
}
~~~

- creates the Table "TableUrl"
- Table has fields "urlOriginal", "urlMod"
- The foreign key `id_LCPModelTableLCP` is added by method `setForeignKey()`
- generates a DataType Class "DataType/DTTableUrl.php" in the Module where the TableUrl resides

---

_Usage_

~~~php
$oTableUrl = new TableUrl($aDbConfig);
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

<a id="3-2"></a>

### 3.2. Explained

_Fields_

~~~php
/**
 * @var array
 */
protected $aFields = array(
    'name'          => 'varchar(255)  COLLATE utf8mb4_bin NOT NULL    COMMENT "Company"',
    'ip'            => 'varchar(19)   COLLATE utf8mb4_bin NOT NULL    COMMENT "IP Adresse"',
    'success'       => 'int(1)        COMMENT ""',
    'datetimeStart' => 'datetime      COMMENT "Phase Beginn"',
    'datetimeEnd'   => 'datetime      COMMENT "Phase Ende"',
    'kwStart'       => 'int(2)        NOT NULL COMMENT "KW Start"',
    'kwEnd'         => 'int(2)        NOT NULL  COMMENT "KW Ende"',
    'description'   => 'text          COLLATE utf8mb4_bin NOT NULL    COMMENT "Beschreibung"'
);
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
    ->set_pass('$1y$10$a8znPSGLJxKqKHbKi9u8ee7Vc67CeRAHAK2cd1MQX3etm5fdOkRH4')
    ->set_firstname('Portal')
    ->set_lastname('Admin')
    ->set_email('foo@example.com')
    ->set_timezone('Europe/Berlin')
    ->set_description('darf ClientAdmin anlegen')
    ->set_stampChange($sNow)
    ->set_stampCreate($sNow));
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
                    ->set_mOptional1('LIKE')
                    ->set_sValue('2021-06-19')
             ); 
    );
    
    // get Datasets with sort order
    $aDataType = $this->retrieve(
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('stampChange')
                ->set_mOptional1('LIKE')
                ->set_sValue('2021-06-19')
        ),
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sValue('ORDER BY id DESC')
        )
);
~~~

_`updateTupel`_

~~~php
// deliver the appropriate (modified) DataType Object to the method
$bSuccess = $this->updateTupel($oTableDataType);
~~~

- the equivalent dataset tupel with object's `id` will be updated.

_`update`_

~~~php
$bSuccess = $this->update(
    $oTableDataType,
    // set
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_key('stampChange')
                ->set_sValue('2021-06-19 00:00:00')
        ),
    // where
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_key('stampChange')
                ->set_mOptional1('<')
                ->set_sValue('2021-06-19 00:00:00')
        ),
);
~~~

_`deleteTupel`_

~~~php
// deliver the appropriate DataType Object to the method
$bSuccess = $this->delete($oTableDataType);
~~~

_`delete`_

~~~php
$bSuccess = $this->delete(
    // set
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_key('stampChange')
                ->set_sValue('2021-06-19 00:00:00')
        )
);
~~~

---

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

<a id="4"></a>

## 4. Events

~~~text
db.model.db.setSqlLoggingState.exception
db.model.db.setForeignKey.exception
db.model.db.checkIfTableExists.exception
db.model.db.createTable.exception
db.model.db.synchronizeFields.exception
db.model.db.synchronizeFields.delete.exception
db.model.db.synchronizeFields.insert.exception
db.model.db.synchronizeFields.update.exception
db.model.db.create.sql
db.model.db.createTable.sql
db.model.db.insert.sql
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

<a id="4-1"></a>

### 4.1. Logging SQL

you can log SQL queries by listening to events.

create a file `sql.php` in the event folder of your myMVC module
and declare the bindings as follows.

_`/modules/{MODULE}/etc/event/sql.php`_
~~~php
#-------------------------------------------------------------
# declare bindings

$aEvent = [
    'db.model.db.create.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
    'db.model.db.insert.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
    'db.model.db.retrieve.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
    'db.model.db.update.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
    'db.model.db.delete.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
    'db.model.db.createTable.sql' => array(
        function(\MVC\DataType\DTArrayObject $oDTArrayObject) {
            \MVC\Log::write($oDTArrayObject->getDTKeyValueByKey('sSql')->get_sValue(), 'sql.log');
        }
    ),
];

#-------------------------------------------------------------
# process: bind the declared ones

\MVC\Event::processBindConfigStack($aEvent);
~~~
