<?php

/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998,1999 SH Online Dienst GmbH
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.23 1999/10/24 12:15:21 kk Exp $
 *
 */ 

class DB_Sql {
  
  /* public: connection parameters */
  var $Host     = "";
  var $Database = "";
  var $User     = "";
  var $Password = "";

  /* public: configuration parameters */
  var $Auto_Free     = 0;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Halt_On_Error = "report"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  var $Seq_Table     = "db_sequence";

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.2";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;
  


  /* public: constructor */
  function DB_Sql($query = "") {
      $this->query($query);
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function query_id() {
    return $this->Query_ID;
  }

  /* public: connection management */
  function connect($Database = "", $Host = "", $User = "", $Password = "") {
    /* Handle defaults */
	if(is_array($GLOBALS["mysqlsettings"])) {
		if(ereg("/home/webtastic/html",$_SERVER["DOCUMENT_ROOT"]) or $_SERVER["HOSTNAME"]=="ss.postvak.net" or $_SERVER["HOSTNAME"]=="vpn.postvak.net" or $_SERVER["HOSTNAME"]=="vpnonline.postvak.net" or substr($_SERVER["PHP_SELF"],0,21)=="/home/webtastic/html/") {
			if($GLOBALS["mysqlsettings"]["name"]["local"]) $GLOBALS["mysqlsettings"]["name"]["remote"]=$GLOBALS["mysqlsettings"]["name"]["local"];
			if($GLOBALS["mysqlsettings"]["localhost"]) {
				if($GLOBALS["mysqlsettings"]["localhost"]=="ss.postvak.net") {
					$GLOBALS["mysqlsettings"]["host"]="127.0.0.1";				
				} else {
					$GLOBALS["mysqlsettings"]["host"]=$GLOBALS["mysqlsettings"]["localhost"];
				}
			} else {
				$GLOBALS["mysqlsettings"]["host"]="127.0.0.1";
			}
			if(($_SERVER["HTTP_HOST"]=="vpn.postvak.net" or $_SERVER["HTTP_HOST"]=="vpnonline.postvak.net")) {
				$GLOBALS["mysqlsettings"]["host"]="127.0.0.1:13306";
			}
			$GLOBALS["mysqlsettings"]["user"]="dbmysql";
			$GLOBALS["mysqlsettings"]["password"]="sh47fm9G";
		}

		if ("" == $Database) $this->Database = $GLOBALS["mysqlsettings"]["name"]["remote"];
		if ("" == $Host) $this->Host = $GLOBALS["mysqlsettings"]["host"];
		if ("" == $User) $this->User = $GLOBALS["mysqlsettings"]["user"];
		if ("" == $Password) $this->Password = $GLOBALS["mysqlsettings"]["password"];
	}
	    if ("" == $Database)
	      $Database = $this->Database;
	    if ("" == $Host)
	      $Host     = $this->Host;
	    if ("" == $User)
	      $User     = $this->User;
	    if ("" == $Password)
	      $Password = $this->Password;

    /* establish connection, select database */
    if ( 0 == $this->Link_ID ) {

#	if($_SERVER["HOSTNAME"]=="bl.postvak.net") {    
#		$this->Link_ID=@mysql_connect($Host, $User, $Password,MYSQL_CLIENT_COMPRESS);
#	} else {
		$this->Link_ID=@mysql_pconnect($Host, $User, $Password);
#	}
      if (!$this->Link_ID) {
        $this->halt("pconnect($Host, $User, \$Password) failed: ".mysql_error());
        return 0;
      }

      if (!@mysql_select_db($Database,$this->Link_ID)) {
        $this->halt("cannot use database ".$this->Database);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }

  /* public: perform a query */
  function query($Query_String) {
    $this->lastquery=$Query_String;
    $this->lq=$Query_String;

    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

    if ($this->Debug)
      printf("Debug: query = %s<br>\n", $Query_String);

    $this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    if(mysql_errno()==2006) {
	    	if($GLOBALS["wt_error_handler"]) {
#	    		trigger_error("lost mysql connection",E_USER_NOTICE);
	    		$this->halt("lost mysql connection");
		}
		$this->Link_ID=0;
		$this->connect();
		$this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    }
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: walk result set */
  function next_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = @mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }

  /* public: position in result set */
  function seek($pos = 0) {
    $status = @mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    else {
      $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows");

      /* half assed attempt to save the day, 
       * but do not consider this documented or even
       * desireable behaviour.
       */
      @mysql_data_seek($this->Query_ID, $this->num_rows());
      $this->Row = $this->num_rows;
      return 0;
    }

    return 1;
  }

  /* public: table locking */
  function lock($table, $mode="write") {
    $this->connect();
    
    $query="lock tables ";
    if (is_array($table)) {
      while (list($key,$value)=each($table)) {
        if ($key=="read" && $key!=0) {
          $query.="$value read, ";
        } else {
          $query.="$value $mode, ";
        }
      }
      $query=substr($query,0,-2);
    } else {
      $query.="$table $mode";
    }
    $res = @mysql_query($query, $this->Link_ID);
    if (!$res) {
      $this->halt("lock($table, $mode) failed.");
      return 0;
    }
    return $res;
  }
  
  function unlock() {
    $this->connect();

    $res = @mysql_query("unlock tables");
    if (!$res) {
      $this->halt("unlock() failed.");
      return 0;
    }
    return $res;
  }


  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return @mysql_affected_rows($this->Link_ID);
  }

  function error_id() {
    return @mysql_errno($this->Query_ID);
  }

  function insert_id() {
    return @mysql_insert_id($this->Link_ID);
  }

  function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }

  function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }

  /* public: shorthand notation */
  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  function f($Name) {
    return $this->Record[$Name];
  }

  function p($Name) {
    print $this->Record[$Name];
  }

  /* public: sequence numbers */
  function nextid($seq_name) {
    $this->connect();
    
    if ($this->lock($this->Seq_Table)) {
      /* get sequence number (locked) and increment */
      $q  = sprintf("select nextid from %s where seq_name = '%s'",
                $this->Seq_Table,
                $seq_name);
      $id  = @mysql_query($q, $this->Link_ID);
      $res = @mysql_fetch_array($id);
      
      /* No current value, make one */
      if (!is_array($res)) {
        $currentid = 0;
        $q = sprintf("insert into %s values('%s', %s)",
                 $this->Seq_Table,
                 $seq_name,
                 $currentid);
        $id = @mysql_query($q, $this->Link_ID);
      } else {
        $currentid = $res["nextid"];
      }
      $nextid = $currentid + 1;
      $q = sprintf("update %s set nextid = '%s' where seq_name = '%s'",
               $this->Seq_Table,
               $nextid,
               $seq_name);
      $id = @mysql_query($q, $this->Link_ID);
      $this->unlock();
    } else {
      $this->halt("cannot lock ".$this->Seq_Table." - has it been created?");
      return 0;
    }
    return $nextid;
  }

  /* public: return table metadata */
  function metadata($table='',$full=false) {
    $count = 0;
    $id    = 0;
    $res   = array();

    /*
     * Due to compatibility problems with Table we changed the behavior
     * of metadata();
     * depending on $full, metadata returns the following values:
     *
     * - full is false (default):
     * $result[]:
     *   [0]["table"]  table name
     *   [0]["name"]   field name
     *   [0]["type"]   field type
     *   [0]["len"]    field length
     *   [0]["flags"]  field flags
     *
     * - full is true
     * $result[]:
     *   ["num_fields"] number of metadata records
     *   [0]["table"]  table name
     *   [0]["name"]   field name
     *   [0]["type"]   field type
     *   [0]["len"]    field length
     *   [0]["flags"]  field flags
     *   ["meta"][field name]  index of field named "field name"
     *   The last one is used, if you have a field name, but no index.
     *   Test:  if (isset($result['meta']['myfield'])) { ...
     */

    // if no $table specified, assume that we are working with a query
    // result
    if ($table) {
      $this->connect();
      $id = @mysql_list_fields($this->Database, $table);
      if (!$id)
        $this->halt("Metadata query failed.");
    } else {
      $id = $this->Query_ID; 
      if (!$id)
        $this->halt("No query specified.");
    }
 
    $count = @mysql_num_fields($id);

    // made this IF due to performance (one if is faster than $count if's)
    if (!$full) {
      for ($i=0; $i<$count; $i++) {
        $res[$i]["table"] = @mysql_field_table ($id, $i);
        $res[$i]["name"]  = @mysql_field_name  ($id, $i);
        $res[$i]["type"]  = @mysql_field_type  ($id, $i);
        $res[$i]["len"]   = @mysql_field_len   ($id, $i);
        $res[$i]["flags"] = @mysql_field_flags ($id, $i);
      }
    } else { // full
      $res["num_fields"]= $count;
    
      for ($i=0; $i<$count; $i++) {
        $res[$i]["table"] = @mysql_field_table ($id, $i);
        $res[$i]["name"]  = @mysql_field_name  ($id, $i);
        $res[$i]["type"]  = @mysql_field_type  ($id, $i);
        $res[$i]["len"]   = @mysql_field_len   ($id, $i);
        $res[$i]["flags"] = @mysql_field_flags ($id, $i);
        $res["meta"][$res[$i]["name"]] = $i;
      }
    }
    
    // free the result only if we were called on a table
    if ($table) @mysql_free_result($id);
    return $res;
  }

  /* private: error handling */
function halt($msg) {
	global $HTTP_SERVER_VARS,$HOSTNAME,$SERVER_NAME;
	$this->Error = @mysql_error($this->Link_ID);
	$this->Errno = @mysql_errno($this->Link_ID);
	if ($this->Errno==1062) return;
	
	# Bestand en regelnummer bepalen
	$debug=@debug_backtrace();
	if(is_array($debug)) {
		while(list($key,$value)=each($debug)) {
			if($value["class"]=="DB_Sql" and $value["function"] and $value["function"]<>"halt") {
				$filename=$value["file"];
				$linenumber=$value["line"];
			}
		}
	}
	if($_SERVER["DOCUMENT_ROOT"]=="/home/webtastic/html" or $GLOBALS["vars"]["lokale_testserver"]) {
		$errormsg.="</td></tr></table><b>MySQL-fout:</b><p>";
		$errormsg.="<b>Bestand: </b>".ereg_replace("/home/webtastic/html","",$filename)."<br>\n";
		$errormsg.="<b>Regel: </b>".$linenumber."<p>\n";
		$errormsg.="<b>Lastquery:</b> ".$this->lastquery."<p>\n";
		$errormsg.="<b>Class-melding:</b> ".$msg."<p>\n";
		if($this->Error) {
			$errormsg.="<b>MySQL-foutmelding:</b> ".$this->Error." (".$this->Errno.")<p>\n";
		}
    		die($errormsg);
	} else {
	    	if($GLOBALS["wt_error_handler"]) {
	    		if($this->Errno==2006 and $GLOBALS["wt_mysql_lost_nolog"]) {
	    		
	    		} else {
			    	$msg=$msg.($this->Error ? " / ".$this->Error." (".$this->Errno.")" : "");
		    		if($filename) {
			    		trigger_error("_WT_FILENAME_".$filename."_WT_FILENAME__WT_LINENUMBER_".$linenumber."_WT_LINENUMBER_".$msg,E_USER_NOTICE);
		    		} else {
			    		trigger_error($msg,E_USER_NOTICE);
			    	}
			}
	    	}
	}
}

  function haltmsg($msg,$errortype='E_USER_WARNING') {
	$included_files=get_included_files();
#	if($_SERVER["DOCUMENT_ROOT"]<>"/home/webtastic/html") $errormsg.=$_SERVER["REMOTE_ADDR"]." - ";
	$errormsg.=$msg."<BR>";
	$errormsg.=$this->Error." (".$this->Errno.")<br>\n";
	$errormsg.="<b>Filename: </b>".$included_files[count($included_files)-1]."<p>";
	trigger_error($errormsg,$errortype);
  }

  function table_names() {
    $this->query("SHOW TABLES");
    $i=0;
    while ($info=mysql_fetch_row($this->Query_ID))
     {
      $return[$i]["table_name"]= $info[0];
      $return[$i]["tablespace_name"]=$this->Database;
      $return[$i]["database"]=$this->Database;
      $i++;
     }
   return $return;
  }
}

$db=new DB_sql;
$db0=new DB_sql;
$db2=new DB_sql;
$db3=new DB_sql;
$db4=new DB_sql;

?>