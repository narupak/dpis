<?

	// show database detail
	if($_POST['connect'] || $_POST['table_name']) {
    	// connect DB Data Dictionary
        $db = ADONewConnection($_POST['databasetype'], $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear");
        $db->createdatabase = true;
        $result = $db->Connect( $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['databasename'] );
        if(!$result) {
            die("Could not connect to the database.");
        } 
        // connect DB gdatadict
        $insert_db = ADONewConnection($_POST['databasetype'], $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear");
        $insert_db->createdatabase = true;
        $result = $insert_db->Connect( "localhost", "root", "123456", "gdatadict" );
        if(!$result) {
            die("Could not connect to the database.");
        }
        
        // connect DB Data Dictionary
        $insert_db2 = ADONewConnection($_POST['databasetype'], $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear");
        $insert_db2->createdatabase = true;
        $result = $insert_db2->Connect( "localhost", "root", "123456", "gdatadict" );
        if(!$result) {
            die("Could not connect to the database.");
        }
        //$db->debug = true;
        
        
        //
        // insert connection 
        //
        $cmd = "select id from system_config where databasetype = '".$_POST['databasetype']."' and dbhost = '".$_POST['dbhost']."' and 
            dbusername = '".$_POST['dbusername']."' and dbpassword = '".$_POST['dbpassword']."' and databasename = '".$_POST['databasename']."' ";
		$insert_rs = $insert_db->Execute($cmd);
        $totaltrecords=$insert_rs->RecordCount();
        $getit=$insert_rs->GetArray();
		if(! $totaltrecords) {
        	$cmd = "insert into system_config (databasetype,dbhost,dbusername,dbpassword,databasename) values
            ('".$_POST['databasetype']."','".$_POST['dbhost']."','".$_POST['dbusername']."','".$_POST['dbpassword']."','".$_POST['databasename']."')";
            $insert_rs = $insert_db->Execute($cmd);
            $dbid = $insert_db->Insert_ID();
        } else {
        	$dbid = $getit[0]['id'];
        }
        
        $db->SelectDB($_POST['databasename']);
        $cmd = " show tables ";
        $rs = $db->Execute($cmd);
        $totaltrecords=$rs->RecordCount(); 
		$getit=$rs->GetArray();
        $db->SelectDB('gdatadict');
        foreach($getit as $row) {
        	$cmd = "select * from table_prop where table_name = '". $row['Tables_in_dpis35'] . "'";
            $insert_rs = $insert_db->Execute($cmd);          
            $table_row=$insert_rs->RecordCount();
            $get_insert=$insert_rs->GetArray();
            if(!$table_row) {
            	// not record in tablename
                // insert new table 
            	$cmd = "insert into table_prop (table_name) values ('".$row['Tables_in_dpis35']."')";
                $insert_rs = $insert_db->Execute($cmd);
                $id = $insert_db->Insert_ID();
                $tablename_array[$id]['table_name'] = $row['Tables_in_dpis35'];
            } else {
            	// get data into array for dispay
                $id = $get_insert[0]['id'];
			    $tablename_array[$id]['table_name'] = $get_insert[0]['table_name'];
            }
        } // end foreach 
	}

	// insert table detail 
    
    if($_POST['update'] == 'Update Data') {
	    $insert_db->SelectDB("gdatadict");
    	foreach($_POST['field_name'] as $key => $field_name_value) {
    		$cmd = "update field_prop set field_null = '".$_POST['field_null'][$key]."' , field_desc = '".$_POST['field_desc'][$key]."' , field_type = '".$_POST['field_type'][$key]."' , 
            				field_index = '".$_POST['field_index'][$key]."' , field_refer = '".$_POST['field_refer'][$key]."' where id = $key";
			//echo $cmd . "<br>";
            $insert_rs = $insert_db->Execute($cmd);
        }
    }

	// show table detail 
	if($_POST['table_name']) {
	    $db->SelectDB($_POST['databasename']);
        $cmd = "select * from " . $_POST['table_name'];
        $rs = $db->SelectLimit($cmd,1);
        $insert_db->SelectDB("gdatadict");
		for ($i = 0; $i < $rs->FieldCount(); $i++ ) {
        	// check field table
			$fieldobject = $rs->FetchField($i);
            $cmd = "select * from field_prop where table_id = '".$_POST['table_id']."' and field_name = '".$fieldobject->name."'";
            $insert_rs = $insert_db->Execute($cmd);          
            $field_row=$insert_rs->RecordCount();
            $get_insert=$insert_rs->GetArray();
            if(!$field_row) {
            	// insert new field to table
            	$cmd = "insert into field_prop (table_id, field_name,field_type) values ('".$_POST['table_id']."','".$fieldobject->name."','".$fieldobject->type ."(".$fieldobject->max_length.")')";
                $insert_rs = $insert_db->Execute($cmd);
                $id = $insert_db->Insert_ID();
                $fieldname_array[$id]['field_name'] = $fieldobject->name;
                $fieldname_array[$id]['field_prop'] = "$fieldobject->type ($fieldobject->max_length)" ;
            } else {
            	// get field prop
                $id = $get_insert[0]['id'];
			    $fieldname_array[$id]['field_name'] = $fieldobject->name;
                $fieldname_array[$id]['field_prop'] = "$fieldobject->type ($fieldobject->max_length)" ;
                $fieldname_array[$id]['field_null'] = $get_insert[0]['field_null'];
                $fieldname_array[$id]['field_desc'] = $get_insert[0]['field_desc'];
                $fieldname_array[$id]['field_index'] = $get_insert[0]['field_index'];
                $fieldname_array[$id]['field_refer'] = $get_insert[0]['field_refer'];
            } // end if 
		} // end for 
    } // end if $_POST
    
?>