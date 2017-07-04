# SingelMysqlMysqli
Developer: Boy108zon
Single PHP Class for running MySql , MySqli Quries
Note: you can do any changes and use script. plz don't remove author top comment.

You can use it like below:

require_once 'MysqlDatabaseMysqli.php';
$dbfun=new MysqlDatabaseMysqli();
$result_emp=$dbfun->get_records('employee',array('name'=>'amit'));

// For Retrive records from employee table.
$result=$dbfun->get_records('your_table_name');

//edit
$result=$dbfun->save_records('your_table_name',array('salary'=>'500000','name'=>'David'),array('id'=>3),$mode='Edit');

//save
$result=$dbfun->save_records('your_table_name',array('salary'=>'200000','name'=>'Jenny'));

//remove
//$result=$dbfun->remove_records('your_table_name',array('id'=>6));

// direct query
$result=$dbfun->get_direct_query_records($query='select * from employee');

echo "<pre>";
print_r($result);

