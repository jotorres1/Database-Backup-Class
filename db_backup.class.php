<?php

class db_backup{
           
	/*
	* Source Database name
	* Name of the table you are copying from
	*/
	private $_source_db;    
	   
	/*
	* Target Database name
	* Name of the table you are copying to
	*/
	private $_target_db;
	   
	/*
	* Name of the server, usually 'localhost'
	*/
	private $_server;
	
	/*
	* Database username
	*/
	private $_db_user;
	   
	/*
	* Database password
	*/
	private $_db_pwd;        
	   
	/*
	* Source tables
	*/
	private $_source_tables;
	   
	/*
	* Our connection variable
	*/
	private $_conn;
	   
	/*
	* When instantiating this class, be sure to send in
	* hosting information as well as the user credentials
	*/
	public function __construct( $s_db, $t_db, $db_user, $db_pw, $s_server = 'localhost' ){
        /* Give initial values to class variables */
       
        /* Database names */
        $this->_source_db = $s_db;
        $this->_target_db = $t_db;
       
        /* Database user credentials */
        $this->_db_user = $db_user;                 
        $this->_db_pwd = $db_pw;
       
        /* Address of the server */
        $this->_server = $s_server;
       
        /* Initialize tables as an array */
        $this->_source_tables = array();
	}
	   
	public function initiate_backup(){
		/* Start the backup process */
	               
	    $this->connect_to_source();
	    $this->get_tables();
	    $this->connect_to_target();
	    $this->write_backup();
	               
    }		
	   
	private function write_backup(){
		/* Total of tables in DB */
	    $total = count( $this->_source_tables );
	    /* Save the data to new database */
	    for( $i = 0;$i < $total; $i++ ){
	    	$name = $this->_source_tables[$i]['name'];
	        $q = $this->_source_tables[$i]['query'];
	 
	        mysql_query( $q );
	        mysql_query("insert into $name select * from $this->_source_db.$name");
        }
	}
	private function connect_to_target(){
	    /* Select the target database */
	    $db_selected = mysql_select_db( $this->_target_db, $this->_conn );
	    /* Verify that the target database has been selected */
	    if ( ! $db_selected ){
	    	die( "Unable to select target database $this->_target_db: ".mysql_error() );
        }
    }
	   
	private function get_tables(){
	               
		/* Get names of all
	    tables in source database */
	    $result = mysql_query( "SHOW TABLES" );
	           
	    /*  Iterate through the results */
	    while( $row = mysql_fetch_array( $result )){
	    	$name = $row[0];
	        $this_result = mysql_query( "SHOW CREATE TABLE $name");
        	$this_row = mysql_fetch_array( $this_result );
	        $this->_source_tables[] = array( 'name'=>$name, 'query'=>$this_row[1] );
    	}
	}
	private function connect_to_source(){
	    /* Connect to the source database */
	    $this->_conn = mysql_connect( $this->_server, $this->_db_user, $this->_db_pwd );
	    /* Verify if we connect successfully */
	    if( ! $this->_conn ){
	    	die( 'Connection Failed: '.mysql_error() );
        }
	    /* Select the source database */
	    $db_selected = mysql_select_db( $this->_source_db, $this->_conn );
	    /* Verify that the database has been selected */
	    if ( ! $db_selected ){
	    	die( "Unable to select source database $this->_source_db: ".mysql_error() );
        }
	}
}
?>