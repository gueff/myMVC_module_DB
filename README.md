
# myMVC_module_DB

- [1. Requirements](#1)
- [2. Repository](#2)
- [3. Creation](#3)
  - [3.1. Create DB Config](#3-1)
  - [3.2. Creating a concrete Table Class](#3-2)
  - [3.3. Creating a DBInit class that is used for each DB access](#3-3)
- [4. Usage](#4)
  - [4.1. create](#4-1)
  - [4.2. retrieve](#4-2)
  - [4.3. update](#4-3)
  - [4.4. delete](#4-4)
  - [4.5. count](#4-5)
  - [4.6. checksum](#4-6)
  - [4.7. getFieldInfo](#4-7)
  - [4.8. SQL](#4-8)
- [5. Events](#5)
  - [5.1. Logging SQL](#5-1)

---

<a id="1"></a>

## 1. Requirements

- Linux
- php >= 8
  - `pdo` extension
- myMVC 3.3.x
  - `git clone --branch 3.3.x https://github.com/gueff/myMVC.git myMVC_3.3.x`
  - Docs: <https://mymvc.ueffing.net/>
  - github: <https://github.com/gueff/myMVC/tree/3.3.x>

---

<a id="2"></a>

## 2. Repository

- <https://github.com/gueff/myMVC_module_DB>

---

<a id="3"></a>

## 3. Creation

<a id="3-1"></a>

### 3.1. Create DB Config


In your main module's config folder create your DB Config.
(@see https://mymvc.ueffing.net/3.3.x/configuration#Modules-config-folder)


_Db Config example for `develop` environments_
~~~php
//-------------------------------------------------------------------------------------
// Module DB

$aConfig['MODULE']['DB'] = array(

    'db' => array(
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'username' => getenv('db.username'),
        'password' => getenv('db.password'),
        'dbname' => getenv('db.dbname'),
        'charset' => 'utf8'
    ),
    'caching' => array(
        'enabled' => true,
        'lifetime' => '7200'
    ),
    'logging' => array(
        'log_output' => 'FILE',

        // consider to turn it on for develop and test environments only
        'general_log' => strtoupper('on'), # on | off

        // 1) make sure write access is given to the folder
        // as long as the db user is going to write and not the webserver user
        // 2) consider a logrotate mechanism for this logfile as it may grow quickly
        'general_log_file' => '/tmp/db.log'
    )
);
~~~
- here we make use of `getenv()`, which means we store our secrets in the `/.env` file.


<a id="3-2"></a>

### 3.2. Creating a concrete Table Class

_PHP Class_
as a Representation of the DB Table


_file: `modules/Foo/Model/DB/TableUser.php`_
~~~php
<?php

namespace Foo\Model\DB;

use DB\Model\Db;


class TableUser extends Db
{
    /**
     * @var array
     */
    protected $aField = array(
        'email'     => "varchar(255) COLLATE utf8_general_ci NOT NULL",
        'active'    => "int(1) DEFAULT '0' NOT NULL",
        'uuid'      => "varchar(36) COLLATE utf8_general_ci COMMENT 'uuid permanent' NOT NULL",
        'uuidtmp'      => "varchar(36) COLLATE utf8_general_ci COMMENT 'uuid; changes on create|login' NOT NULL",
        'password'  => "varchar(60) COLLATE utf8_general_ci COMMENT 'password_hash()' NOT NULL",
        'nickname'  => "varchar(10) COLLATE utf8_general_ci NOT NULL",
        'forename'  => "varchar(25) COLLATE utf8_general_ci NOT NULL",
        'lastname'  => "varchar(25) COLLATE utf8_general_ci NOT NULL",
    );

    /**
     * @param array $aDbConfig
     * @throws \ReflectionException
     */
    public function __construct(array $aDbConfig = array())
    {
        // basic creation of the table
        parent::__construct(
            $this->aField,
            $aDbConfig
        );
    }
}
~~~

- creates the Table `TableUser`
  - Table has several fields from `email` ... `lastname` as declared in property `$aField`
    - 🛈 The Table fields `id`, `stampChange` and `stampCreate` are added automatically
    - do not add these fields by manually
- generates a DataType Class `DataType/DTFooModelDBTableUser.php`

---

**Creating a Table and adding a Foreign Key**


_file: `modules/Foo/Model/DB/TableUser.php`_
~~~php
<?php

namespace Foo\Model\DB;

use DB\Model\Db;
use DB\DataType\DB\Foreign;

class TableUser extends Db
{
    /**
     * @var array
     */
    protected $aField = array(
        'email'     => "varchar(255) COLLATE utf8_general_ci NOT NULL",
        'active'    => "int(1) DEFAULT '0' NOT NULL",
        'uuid'      => "varchar(36) COLLATE utf8_general_ci COMMENT 'uuid permanent' NOT NULL",
        'uuidtmp'      => "varchar(36) COLLATE utf8_general_ci COMMENT 'uuid; changes on create|login' NOT NULL",
        'password'  => "varchar(60) COLLATE utf8_general_ci COMMENT 'password_hash()' NOT NULL",
        'nickname'  => "varchar(10) COLLATE utf8_general_ci NOT NULL",
        'forename'  => "varchar(25) COLLATE utf8_general_ci NOT NULL",
        'lastname'  => "varchar(25) COLLATE utf8_general_ci NOT NULL",
    );

    /**
     * @param array $aDbConfig
     * @throws \ReflectionException
     */
    public function __construct(array $aDbConfig = array())
    {
        // basic creation of the table
        parent::__construct(
            $this->aField,
            $aDbConfig
        );
        $this->setForeignKey(
            Foreign::create()
                ->set_sForeignKey('id_TableGroup')
                ->set_sReferenceTable('FooModelDBTableGroup')
        );
    }
}
~~~

- creates the Table `TableUser`
  - Table has several fields from `email` ... `lastname` as declared in property `$aField`
    - 🛈 The Table fields `id`, `stampChange` and `stampCreate` are added automatically
    - do not add these fields by manually
- The foreign key `id_TableGroup` -pointing to table `FooModelDBTableGroup`- is added by method `setForeignKey()`
- generates a DataType Class `DataType/DTFooModelDBTableUser.php`

---

<a id="3-3"></a>

### 3.3. Creating a DBInit class that is used for each DB access


_file: `modules/Foo/Model/DB.php`_
~~~php
<?php

/**
 * - register your db table classes as static properties.
 * - add a doctype to each static property
 * - these doctypes must contain the vartype information about the certain class
 * @example
 *      @var Foo\Model\DB\TableUser
 *      public static $oFooModelDBTableUser;
 * ---
 * [!]  it is important to declare the vartype expanded with a full path
 *      avoid to make use of `use ...` support
 *      otherwise the classes could not be read correctly
 */

namespace Foo\Model;

use DB\Model\DbInit;
use DB\Trait\DbInitTrait;

class DB extends DbInit
{
    use DbInitTrait;
    
    /**
     * @var \Foo\Model\DB\TableUser
     */
    public static $oFooModelDBTableUser;
}
~~~

---

<a id="4"></a>

## 4. Usage


In your main **Controller** class just create a new Instanciation of your DBInit class.
A good place is the `__construct()` method.

~~~php
namespace Foo\Controller;

use Foo\Model\DB;

public function __construct ()
{
    DB::init();
}
~~~

after that you can access your TableClass from everywhere - even from frontend templates:


_Usage_
~~~php
DB::$oFooModelDBTableUser->...<method>...
~~~

<a id="4-1"></a>

### 4.1. create

_`create` (INSERT)_
therefore an object of its related Datatype must be instaciated and given to the method `create`.
Here e.g. with Datatype "DTFooModelDBTableUser" to TableClass "modules/Foo/Model/DB/TableUser":

~~~php
DB::$oFooModelDBTableUser->create(
    DTFooModelDBTableUser::create()
        ->set_id_TableGroup(1)
        ->set_uuid(Strings::uuid4())
        ->set_email('foo@example.com')
        ->set_forename('foo')
        ->set_lastname('bar')
        ->set_nickname('foo')
        ->set_password(password_hash('...password...', PASSWORD_DEFAULT))
        ->set_active(1)
        ->set_stampChange(date('Y-m-d H:i:s'))
        ->set_stampCreate(date('Y-m-d H:i:s'))
);
~~~


<a id="4-2"></a>

#### 4.2. retrieve

`retrieveTupel` asks for a specific Tupel and returns the DataType Object according to the requested Table.


_`retrieveTupel` - identified by `id`_
~~~php
/** @var \Foo\DataType\DTFooModelDBTableUser $oDTFooModelDBTableUser */
$oDTFooModelDBTableUser = DB::$oFooModelDBTableUser->retrieveTupel(
    DTFooModelDBTableUser::create()
        ->set_id(2)
)
~~~
- get User Object whose id=2


`retrieve` returns an array of DataType Objects according to the requested Table.

_`retrieve`: get all Datasets_
~~~php
/** @var \Foo\DataType\DTFooModelDBTableUser[] $aDTFooModelDBTableUser */
$aDTFooModelDBTableUser = DB::$oFooModelDBTableUser->retrieveTupel();
~~~

_`retrieve`: get specific Datasets_
~~~php
/** @var \Foo\DataType\DTFooModelDBTableUser[] $aDTFooModelDBTableUser */
$aDTFooModelDBTableUser = DB::$oFooModelDBTableUser->retrieve(
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('stampChange')
                ->set_mOptional1('LIKE')
                ->set_sValue('2021-06-19')
            );
);
~~~

_`retrieve`: get Datasets with sort order_
~~~php
/** @var \Foo\DataType\DTFooModelDBTableUser[] $aDTFooModelDBTableUser */
$aDTFooModelDBTableUser = DB::$oFooModelDBTableUser->retrieve(
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('email')
                ->set_mOptional1('LIKE')
                ->set_sValue('%@example.com%')
        ),
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sValue('ORDER BY id ASC')
        )
);
~~~

_`retrieve`: get first 30 Datasets (LIMIT 0,30)_
~~~php
/** @var \Foo\DataType\DTFooModelDBTableUser[] $aDTFooModelDBTableUser */
$aDTFooModelDBTableUser = DB::$oFooModelDBTableUser->retrieve(
    null,
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sValue('LIMIT 0,30')
        )
)
~~~


<a id="4-3"></a>

#### 4.3. update


_`updateTupel`: update this specific Tupel - identified by `id`_
~~~php
/** @var boolean $bSuccess */
$bSuccess = DB::$oFooModelDBTableUser->updateTupel(
    DTFooModelDBTableUser::create()
        ->set_id(1)
        ->set_nickname('XYZ')
);
~~~
- the equivalent dataset tupel with object's `id` will be updated.

_`update`: update all Tupel which are affected by the where clause_
~~~php
/** @var boolean $bSuccess */
$bSuccess = DB::$oFooModelDBTableUser->update(
    DTFooModelDBTableUser::create()
        ->set_active('1'),
    // where
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('active')
                ->set_mOptional1('=')
                ->set_sValue('0')
        )
);
~~~


<a id="4-4"></a>

#### 4.4. delete

_`deleteTupel`: delete this specific Tupel - identified by `id`_
~~~php
/** @var boolean $bSuccess */
$bSuccess = DB::$oFooModelDBTableUser->deleteTupel(
    DTFooModelDBTableUser::create()
        ->set_id(2)
)
~~~

_`delete`: delete all Tupel which are affected by the where clause_
~~~php
$bSuccess = DB::$oFooModelDBTableUser->delete(
    // where
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('stampCreate')
                ->set_mOptional1('<')
                ->set_sValue('2023-06-19 00:00:00')
        )
);
~~~

<a id="4-5"></a>

### 4.5. count

~~~php
// Amount of all Datasets
$iAmount = DB::$oFooModelDBTableUser->count();

// Amount of specific Datasets
$iAmount = DB::$oFooModelDBTableUser->count(
    DTArrayObject::create()
        ->add_aKeyValue(
            DTKeyValue::create()
                ->set_sKey('stampChange')
                ->set_mOptional1('=')
                ->set_sValue('2021-06-19')
    )
);
~~~

<a id="4-6"></a>

### 4.6. checksum

~~~php
// Returns a checksum of the table
$iChecksum = DB::$oFooModelDBTableUser->checksum();
~~~

<a id="4-7"></a>

### 4.7. getFieldInfo

returns array with table fields info

~~~php
$aFieldInfo = DB::$oFooModelDBTableUser->getFieldInfo();
~~~

_example return_

~~~
// type: array, items: 9
[
    'id_TableGroup' => [
        'Field' => 'id_TableGroup',
        'Type' => 'int(11)',
        'Null' => 'YES',
        'Key' => 'MUL',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'int',
    ],
    'email' => [
        'Field' => 'email',
        'Type' => 'varchar(255)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'active' => [
        'Field' => 'active',
        'Type' => 'int(1)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => '0',
        'Extra' => '',
        'php' => 'int',
    ],
    'uuid' => [
        'Field' => 'uuid',
        'Type' => 'varchar(36)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'uuidtmp' => [
        'Field' => 'uuidtmp',
        'Type' => 'varchar(36)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'password' => [
        'Field' => 'password',
        'Type' => 'varchar(60)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'nickname' => [
        'Field' => 'nickname',
        'Type' => 'varchar(10)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'forename' => [
        'Field' => 'forename',
        'Type' => 'varchar(25)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
    'lastname' => [
        'Field' => 'lastname',
        'Type' => 'varchar(25)',
        'Null' => 'NO',
        'Key' => '',
        'Default' => NULL,
        'Extra' => '',
        'php' => 'string',
    ],
]
~~~

<a id="4-8"></a>

#### 4.8. SQL

_`SQL` example_
~~~php
/**
 * @param DTLCPModelTableLCP $oDTLCPModelTableLCP
 * @return array
 */
public function getUrlAndClick(DTLCPModelTableLCP $oDTLCPModelTableLCP)
{
    $sSql = "
        SELECT 
            CLICK.*, 
            URL.urlOriginal, 
            URL.urlMod
        FROM        `LCPModelTableClick`    AS CLICK
        RIGHT JOIN  `LCPModelTableUrl`      AS URL      ON CLICK.id_LCPModelTableUrl = URL.id
        WHERE 1
        AND URL.id_LCPModelTableLCP = " . (int) $oDTLCPModelTableLCP->get_id();

    $aResult = $this->oDbPDO->fetchAll($sSql);

    return $aResult;
}
~~~

---

<a id="5"></a>

## 5. Events

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

<a id="5-1"></a>

### 5.1. Logging SQL

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
