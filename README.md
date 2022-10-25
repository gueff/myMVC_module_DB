
# myMVC_module_DB

## Requirements

- Linux
- php >= 7.4
    - `pdo` extension
- myMVC 3.2.x
  - `git clone --branch 3.2.x https://github.com/gueff/myMVC.git myMVC_3.2.x`
  - github: https://github.com/gueff/myMVC/tree/3.2.x   
  - Docs: https://mymvc.ueffing.net/
    
## Examples

_PHP Class_  
as a Representation of the DB Table

_Most simple_    
~~~php
<?php

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

___


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

        $this->synchronizeFields();
        $this->generateDataType();
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
