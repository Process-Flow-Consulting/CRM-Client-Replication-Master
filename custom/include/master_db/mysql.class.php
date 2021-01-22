<?php
class MasterMySQL
{
  # Our connection holder.
  private $db_con = null;
  # The connection details
  private $db_host = null;
  private $db_user = null;
  private $db_pass = null;
  private $db_name = null;
  private $db_persist = null;

  #
  # Default Constructor, used when doing something such as:
  # $db = new MySQL('localhost', 'root', '', null, false);
  # Basically an alias of $this->connect();
  #
  public function __construct($db_host = null, $db_user = null, $db_pass = null, $db_name = null, $db_persist = false)
  {
    # They might not be trying at all to connect through the
    # default constructor, but they might, so see if the
    # database host is empty or not :)
    if(!empty($db_host))
    {
      # Lets use the connect function...
      $this->connect($db_host, $db_user, $db_pass, $db_name, $db_persist);
    }
  }

  #
  # Connect to the MySQL Server.
  # bool MySQL::connect(string $db_host, string $db_user, string $db_pass[, string $db_name = null[, bool $db_persist = false]]);
  #   string $db_host - The hostname or address (Like IP) of the MySQL
  #                     server you wish to connect to, a lot of the times
  #                     it is localhost.
  #
  #   string $db_user - Your MySQL Username to identify as.
  #
  #   string $db_pass - The MySQL Password of the MySQL Username.
  #
  #   string $db_name - The Database you would like to select and use by
  #                     default, however, not required and can be later
  #                     set with $this->select_db(string $db_name);
  #
  #   bool $db_persist - Whether or not to connect with a persistent
  #                      connection with the MySQL Server, if you are
  #                      unsure of what this means, you can take a look
  #                      at www.php.net/manual/en/features.persistent-connections.php
  #
  #   returns bool - If the connection was successful, you will get a true
  #                  however if connecting failed, you will get a false.
  #                  Need to know what happened? Check out $this->error();
  #
  public function connect($db_host, $db_user, $db_pass, $db_name = null, $db_persist = false)
  {
    # So do you want a persistent connection or not?
    # If you want more information about the difference
    # check out php.net's super useful website:
    # www.php.net/manual/en/features.persistent-connections.php
   /* 
    * commented by : Ashutosh
    * date : 26 Feb 2013 
    * purpose : do not allow persistant connection 
    if($db_persist)
      $this->db_con = @mysql_pconnect($db_host, $db_user, $db_pass);
    else
     
    */
      $this->db_con = @mysql_connect($db_host, $db_user, $db_pass,true);

    # So did we connect? o-O
    if(!$this->db_con)
      return false;

    # I guess so, do we want to select a database? If not,
    # we won't bother, whatever floats your boat...
    if(!empty($db_name))
      if(!$this->select_db($db_name))
        return false;

    # Save the details ;)
    # Why? This is so we can switch users and stuff
    # and we might need the details again...
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_persist = (bool)$db_persist;

    # If we are still going, we must have done it all
    # successfully... So return true :)
    return true;
  }

  #
  # Close the current MySQL connection.
  # bool MySQL::close();
  #   returns bool - If the connection is closed successfully
  #                  you will get true, otherwise on failure a
  #                  false will be returned.
  #
  public function close()
  {
    # Of course you must be connected to be able to close!
    if($this->db_con)
    {
      # Just close it ;)
      $success = @mysql_close($this->db_con);
      # Was closing the connection a success?
      # If so, remove all our stuffs.
      if($success)
      {
        $this->db_host = null;
        $this->db_user = null;
        $this->db_pass = null;
        $this->db_name = null;
        $this->db_persist = false;
        $this->db_con = null;
      }
      return $success ? true : false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Select your default database.
  # bool MySQL::select_db(string $db_name);
  #   string $db_name - The name of the database you want
  #                     to use as the default one for the
  #                     current connection.
  #
  #   returns bool - If your database was successfully selected
  #                  you will get a bool of true, otherwise false
  #                  and an error notice.
  #
  public function select_db($db_name)
  {
    # We need to be connected...
    if($this->db_con)
    {
      # Just select it and we are done.
      $success = @mysql_select_db($db_name, $this->db_con);
      # If its a success, save the new database name...
      if($success)
        $this->db_name = $db_name;
      return $success ? true : false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Query the MySQL Server.
  # mixed MySQL::query(string $db_query[, bool $unbuffered = false]);
  #   string $db_query - The query you want to send to the
  #                      MySQL Server to be parsed and the
  #                      result will of course, be returned.
  #
  #   bool $unbuffered - Whether or not to do an unbuffered
  #                      query. Not sure what this means?
  #                      Basically you want start messing
  #                      around with the results of the query
  #                      without the query needing to be 100%
  #                      executed. Very useful when you are
  #                      expecting a lot of results.
  #
  #   returns mixed - Depending upon if the query was successful
  #                   you might get a MySQL resource or a false.
  #                   Obviously if the query was a success you will
  #                   get a resource back, otherwise if the query
  #                   failed you will get a false.
  #
  public function query($db_query, $unbuffered = false)
  {
    # We must be connected!!!
    if($this->db_con)
    {
      # So unbuffered or not?
      if($unbuffered)
        return @mysql_unbuffered_query($db_query, $this->db_con);
      else
        return @mysql_query($db_query, $this->db_con);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Escape a string safely with the MySQL Server.
  # string MySQL::escape(string $dirtyStr);
  #   string $dirtyStr - The dirty string that needs to be
  #                      sanitized so you can be sure that the
  #                      data is safe to put in a MySQL Query.
  #
  #   returns string - The cleaned string will be returned.
  #
  #   NOTE: Only PHP 4.3.0 and above have the ability to have
  #         the MySQL Server itself clean the string, which is
  #         a lot safer, however older PHP versions must then
  #         use the deprecated function which PHP cleans the
  #         string itself, which is OK, just the MySQL one is better.
  #
  public function escape($dirtyStr)
  {
    # We must be connected! As the string is cleaned by the server.
    if($this->db_con)
    {
      # So does mysql_real_escape_string exist?
      if(function_exists('mysql_real_escape_string'))
        return @mysql_real_escape_string($dirtyStr, $this->db_con);
      else
        # Nope, use the old one.
        return @mysql_escape_string($dirtyStr);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get the number of affected rows from the last query
  # to the server.
  # int MySQL::affected_rows();
  #   returns int - Returns the number of affected rows from
  #                 the last REPLACE, INSERT, UPDATE or DELETE
  #                 query.
  #
  # NOTE: When doing a REPLACE query you will get the number
  #       not only inserted/updated rows but also the number
  #       deleted as well.
  #
  public function affected_rows()
  {
    # Yes, must be connected...
    if($this->db_con)
      # Simple... as most are...
      return @mysql_affected_rows($this->db_con);
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Move the internal pointer in a MySQL Query result.
  # bool MySQL::data_seek(resource $db_result, int $row_number);
  #   resource $db_result - The MySQL resource of a MySQL Query.
  #
  #   int $row_number - The desired row number to be pointed to.
  #
  #   returns bool - Returns whether or not moving the pointer
  #                  was a success.
  #
  public function data_seek($db_result, $row_number)
  {
    if($this->db_con)
      # Lets try it...
      return @mysql_data_seek($db_result, (int)$row_number);
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # The last MySQL Error Number from the last MySQL operation
  # int MySQL::errno();
  #   returns int - Returns the last MySQL Error Number from the
  #                 last MySQL operation and 0 if no error occurred.
  #
  public function errno()
  {
    # I guess you don't need a connection this time...
    return @mysql_errno($this->db_con ? $this->db_con : null);
  }

  #
  # The latest MySQL Error from the MySQL Server
  # string MySQL::error();
  #   returns string - This function can be very useful when
  #                    debugging, so when a query fails and
  #                    you are not sure why, check out this
  #                    function to see if it can help you fix
  #                    the problem.
  #
  public function error()
  {
    # You don't need a connection :)
    return @mysql_error($this->db_con ? $this->db_con : null);
  }

  #
  # Fetch a row as an array from the given MySQL result.
  # mixed MySQL::fetch_array(resource $db_result[, int $result_type = MYSQL_BOTH]);
  #   resource $db_result - A MySQL result from a MySQL Query.
  #
  #   int $result_type - The result type to get, valid options
  #                      are MYSQL_NUM, MYSQL_ASSOC and MYSQL_BOTH
  #
  #   returns mixed - You will get an array if there are results left
  #                   otherwise it issues false.
  #
  public function fetch_array($db_result, $result_type = MYSQL_BOTH)
  {
    # No connection needed here ;)
    return @mysql_fetch_array($db_result, (int)$result_type);
  }

  #
  # Fetch an associative array from the given MySQL result.
  # This would be like doing MySQL::fetch_array($db_result, MYSQL_ASSOC);
  # mixed MySQL::fetch_assoc(resource $db_result);
  #   resource $db_result - The MySQL result from a MySQL Query.
  #
  #   returns mixed - If there are any rows left, you will get an
  #                   array, otherwise you will get a false upon
  #                   running into the end of the line.
  #
  public function fetch_assoc($db_result)
  {
    # No connection needed here either :D
    return @mysql_fetch_assoc($db_result);
  }

  #
  # Fetch an object with the data of a given row from a MySQL result.
  # mixed MySQL::fetch_object(resource $db_result);
  #   returns mixed - Upon success you will be returned an object
  #                   otherwise a false upon failure, aka no more
  #                   rows are available!
  #
  public function fetch_object($db_result)
  {
    # Once again... no connection needed!
    return @mysql_fetch_object($db_result);
  }

  #
  # Gets the specified column name by the field offset.
  # mixed MySQL::field_name(resource $db_result, int $field_offset);
  #   resource $db_result - A MySQL Query result.
  #
  #   int $field_offset - The field offset of the wanted field,
  #                       starts at 0.
  #
  #   returns mixed - If the field actually exists, you will
  #                   get the name of the field, as expected
  #                   however if the field does not exist at
  #                   that offset you will get a false returned.
  #
  public function field_name($db_result, $field_offset)
  {
    # No connection ^(._.)^
    return @mysql_field_name($db_result, (int)$field_offset);
  }

  #
  # Returns a number indexed array from the result.
  # mixed MySQL::fetch_row(resource $db_result);
  #   returns mixed - If there is actually a row to be returned
  #                   then you will get an array, if there are
  #                   no more rows, you will get a false returned.
  #
  public function fetch_row($db_result)
  {
    # No connection either! CRAZY!
    return @mysql_fetch_row($db_result);
  }

  #
  # Free all resources associated with the given result.
  # bool MySQL::free_result(resource $db_result);
  #   resource $db_result - The MySQL resource from a MySQL Query.
  #
  #   returns bool - If the resources were successfully freed you
  #                  will get a true, otherwise false of failure.
  #
  public function free_result($db_result)
  {
    # Need I say anything..? www.php.net/mysql_free_result
    return @mysql_free_result($db_result);
  }

  #
  # Gets the Client MySQL version
  # string MySQL::get_client_info();
  #   returns string - You will get the Client Library version.
  #
  public function get_client_info()
  {
    return @mysql_get_client_info();
  }

  #
  # Gives you the MySQL Server version
  # string MySQL::get_server_info();
  #   returns string - Returns the MySQL Server version like:
  #                    5.0.67-community
  #
  public function get_server_info()
  {
    # Of course we need to be connected for this!!!
    if($this->db_con)
      return @mysql_get_server_info($this->db_con);
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Gives you information about the last MySQL Query.
  # mixed MySQL::last_info();
  #   returns mixed - If anything about the last query
  #                   is available you will get a string
  #                   otherwise you will get a false.
  #
  public function last_info()
  {
    # Need a connection!!!
    if($this->db_con)
      return @mysql_info($this->db_con);
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Very useful, gets the last inserted ID
  # int MySQL::last_id();
  #   returns int - If there was an AUTO_INCREMENT value in the
  #                 last MySQL Query, you will get that number
  #                 but if no queries have an AUTO_INCREMENT you
  #                 will get a 0.
  #
  public function last_id()
  {
    # Of course you need a connection... :)
    if($this->db_con)
      return @mysql_insert_id($this->db_con);
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # The total number of fields (columns) from the given
  # MySQL result.
  # mixed MySQL::num_fields(resource $db_result);
  #   resource $db_result - A MySQL Query result, a SELECT.
  #
  #   returns mixed - The number of fields in the MySQL Query SELECT
  #                   resource, however if nothing, a false.
  #
  public function num_fields($db_result)
  {
    # Nope, no connection needed!
    return @mysql_num_fields($db_result);
  }

  #
  # The number of rows from a SHOW or SELECT Query.
  # mixed MySQL::num_rows(resource $db_result);
  #   resource $db_result - A MySQL resource from a SHOW or SELECT Query.
  #
  #   returns mixed - If the resource is valid you will get a integer,
  #                   however, if not a false for failure.
  #
  public function num_rows($db_result)
  {
    # No connection needed as well...
    return @mysql_num_rows($db_result);
  }

  #
  # Returns the SQL Query to create the given table.
  # mixed MySQL::show_create(string $tbl_name);
  #   string $tbl_name - The table name you wish to get the
  #                      CREATE TABLE Query for.
  #
  #   returns mixed - If the table actually exists, you will
  #                   get a string, which is the actual CREATE
  #                   TABLE query. If the table does not exist
  #                   you will get a false.
  #
  public function show_create($tbl_name)
  {
    # Yes... we definitely need a connection.
    if($this->db_con)
    {
      # Okay, lets see if the table exists.
      $db_result = $this->query('SHOW CREATE TABLE `'. $tbl_name. '`');
      # Anything?
      if($this->num_rows($db_result))
      {
        # So get the query.
        @list($show_create) = $this->fetch_row($db_result);
        $this->free_result($db_result);
        # Return the query and we are done.
        return $show_create;
      }
      else
        # Nothing, so it must not exist!
        return false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Changes your MySQL User, basically it reimplements mysql_change_user();
  # bool MySQL::change_user(string $new_user, string $new_pass[, string $new_db = null]);
  #   string $new_user - The new MySQL Username to identify as.
  #
  #   string $new_pass - The password of the new MySQL Username.
  #
  #   string $new_db - The new database to select, however if none
  #                    is specified the old one is used, if any.
  #
  #   returns bool - Returns whether or not the username was changed successfully.
  #
  # NOTE: This function roughly implements the old mysql_change_user(); function
  #       in PHP, the way it is done is by closing and making a new connection on
  #       the same host, just without requiring you needing to do so yourself.
  #
  public function change_user($new_user, $new_pass, $new_db = null)
  {
    # We need to be connected of course of course!
    if($this->db_con)
    {
      # Sweet, all it is is closes the old one and connects again :D
      $this->close();
      return $this->connect($this->db_host, $new_user, $new_pass, !empty($new_db) ? $new_db : $this->db_name, $this->db_persist);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }
  
  #
  # Returns the current Client Encoding.
  # string MySQL::client_encoding();
  #   returns string - This returns the current client encoding
  #                    but of course if you are not connected
  #                    a false will be returned.
  #
  public function client_encoding()
  {
    # Connection needed...
    if($this->db_con)
    {
      # Simple to do, just return it ;)
      return @mysql_client_encoding($this->db_con);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Creates a new MySQL Database.
  # bool MySQL::create_db(string $db_name);
  #   string $db_name - The name of the database to be created.
  #
  #   returns bool - Returns whether or not the database was created
  #                  successfully.
  #
  # NOTE: You must have the CREATE privilege assigned to your
  #       MySQL user to do this.
  #
  public function create_db($db_name)
  {
    # Connection..?
    if($this->db_con)
    {
      # Lets try to make the database...
      $db_result = $this->query('CREATE DATABASE `'. $db_name. '`');
      if($db_result)
        # Must have been created successfully!
        return true;
      else
        # Guess not, tough luck!
        return false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Drops the given database name.
  # bool MySQL::drop_db(string $db_name);
  #   string $db_name - The database to drop.
  #
  #   returns bool - Whether or not the database was
  #                  successfully dropped or not.
  #
  # NOTE: You might not have permission to use this
  #       on your MySQL Server, either way, this can
  #       be dangerous! Dropping a database CANNOT
  #       be undone and all data will be lost!
  #
  public function drop_db($db_name)
  {
    # Need a connection...
    if($this->db_con)
    {
      # Lets give it a shot!
      $db_result = $this->query('DROP DATABASE `'. $db_name. '`');
      if($db_result)
        # Must be gone, Congratulations! (I hope)
        return true;
      else
        # Didn't happen, phew!
        return false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Executes a query against the given database, instead of,
  # if selected, the database you used with MySQL::select_db();
  # mixed MySQL::db_query(string $db_query, string $db_name[, bool $permanent = false]);
  #   string $db_query - The query to execute against the given database.
  #
  #   string $db_name - The database name of which to execute the query against.
  #                     Only temporarily, of course (Unless otherwise specified).
  #
  #   bool $permanent - If you choose true, then your database WON'T be switched
  #                     back to the other one before using this method, if you set
  #                     it to false (which is the default) then your database will
  #                     be switched back, if one was selected.
  #
  #   returns mixed - This can return a MySQL resource on a successful
  #                   SELECT, but a false on a failed SELECT. Also on
  #                   UPDATE, INSERT, REPLACE or DELETE it will return a
  #                   bool respectively if it was a success or not.
  #
  # NOTE: PHP does have a built in function for this, however it
  #       is now deprecated, so I have made an implementation of my
  #       own.
  #
  public function db_query($db_query, $db_name, $permanent = false)
  {
    # We need to be connected!
    if($this->db_con)
    {
      # Do you want to switch back after the query?
      if(!$permanent && !empty($this->db_name))
        # Yup, save the current one temporarily
        $old_db = $this->db_name;
      # Okay. Select the new one
      if($this->select_db($db_name))
      {
        # So we switched the database successfully... do the query :)
        $db_result = $this->query($db_query);

        # Got it done, switch back?
        if(!$permanent && isset($old_db))
          $this->select_db($old_db);

        # Return the result...
        return $db_result;
      }
      else
      {
        # Output the error since we switch back...
        trigger_error($this->error(), E_USER_NOTICE);

        # Select the old one :P
        if(!$permanent && isset($old_db))
          $this->select_db($old_db);

        return false;
      }
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get a resource with a list of databases.
  # resource MySQL::list_dbs();
  #   returns resource - If nothing bad occurred, you will
  #                      get a MySQL resource with a list of
  #                      databases which is used with something
  #                      like MySQL::fetch_array(), fetch_assoc(),
  #                      fetch_object(), etc. however if it
  #                      failed you will get a false.
  #
  public function list_dbs()
  {
    # A connection is required.
    if($this->db_con)
    {
      # Lets try!
      return @mysql_list_dbs($this->db_con);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get a resource with a list of tables from a database.
  # resource MySQL::list_tables([string $db_name = null]);
  #   string $db_name - The database to get the table listing from
  #                     though if none are supplied, the current is used.
  #
  #   returns resource - On success, you will get a MySQL resource
  #                      which can be used with MySQL::fetch_array(),
  #                      fetch_assoc(), fetch_object(), etc. however
  #                      if it failed, you will get a false.
  #
  # NOTE: The PHP function for this is DEPRECATED, so this
  #       is an implementation I have done myself.
  #
  public function list_tables($db_name = null)
  {
    # Need a connection...
    if($this->db_con)
    {
      # Lets give the query a try!
      $db_result = $this->query('SHOW TABLES'. (!empty($db_name) ? ' FROM `'. $db_name. '`' : ''));
      # And return it :)
      return $db_result;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Pings the MySQL Server, and if you choose to, you can
  # have it reconnect if the ping failed.
  # bool MySQL::ping([bool $reconnect_on_failure = true]);
  #   bool $reconnect_on_failure - Whether or not to attempt to
  #                                reconnect to the MySQL Server
  #                                if the ping failed. Default is
  #                                set to true.
  #
  #   returns bool - If the ping failed, you will get a false. However
  #                  if $reconnect_on_failure is true, and if it reconnects
  #                  successfully on a ping fail you will get a true, but
  #                  if reconnection on failure, fails, you will get a false.
  #
  public function ping($reconnect_on_failure = true)
  {
    # Yeah, connection, duh...
    if($this->db_con)
    {
      # Ping it.
      $db_ping = @mysql_ping($this->db_con);

      # Hmm, did it fail? And did we want to reconnect?
      if(!$db_ping && $reconnect_on_failure)
      {
        # Reconnect and return the results :P
        return $this->reconnect();
      }

      # Now return whether or not the ping was a success.
      return $db_ping ? true : false;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Reconnect to the MySQL Server, useful for if a ping fails,
  # though you should just do MySQL::ping(true);
  # bool MySQL::reconnect();
  #   returns bool - If the reconnection is successful you will
  #                  get a true, otherwise false for failure.
  #
  # NOTE: This function is used by MySQL::ping() to reconnect
  #       to the MySQL Server if pinging failed.
  # NOTE NOTE: This takes no parameters, this uses the old
  #            saved MySQL details from doing a MySQL connect.
  #
  public function reconnect()
  {
    # We need a connection, or what we this is one.
    if($this->db_con)
    {
      # Try to connect and return the results.
      return $this->connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_persist);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get the MySQL Server Statistics.
  # mixed MySQL::stat([bool $return_array = false]);
  #   bool $return_array - You can have the statistics returned
  #                        as an array instead of a string, default
  #                        is false.
  #
  #   returns mixed - If you are not connected to a MySQL Server, you
  #                   will get a false. If you are connected and set
  #                   $return_array as true, you will recieve an array
  #                   otherwise
  #
  public function stat($return_array = false)
  {
    if($this->db_con)
    {
      # Get the stats.
      $stats = mysql_stat($this->db_con);
      # Return them, array though?
      return $return_array ? $stats : @explode('  ', $stats);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get the Full MySQL Server Statistics.
  # mixed MySQL::full_stats([bool $return_resource = false]);
  #   bool $return_resource - If you just want the resource
  #                           to be used on something like
  #                           MySQL::fetch_assoc(), set this
  #                           to true.
  #
  #   returns mixed - If you are not connected to a MySQL Server
  #                   you will get a false. If you set $return_resource
  #                   to true, you will receive a resource, otherwise an
  #                   array.
  #
  # NOTE: This is somewhat like MySQL::stat(), however a lot more
  #       information can be obtained with this function compared
  #       to MySQL::stat() which only returns a few key statistics.
  #
  public function full_stats($return_resource = false)
  {
    if($this->db_con)
    {
      # Query the server.
      $db_result = $this->query('SHOW STATUS');

      # Want it turned into an array?
      if(!$return_resource && $db_result)
      {
        # Setup a temporary array.
        $temp = array();

        # Loop through the result.
        while($row = $this->fetch_row($db_result))
          # Add it to the array.
          $temp[$row[0]] = $row[1];

        # Now that we are done with the result
        # set db_result to the array for returning.
        $db_result = $temp;
      }

      # Return the db_result, array or not ;)
      return $db_result;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Get the MySQL Variables.
  # mixed MySQL::variables([bool $return_resource = false]);
  #   bool $return_resource - Whether or not to return a resource
  #                           which can be used with MySQL::fetch_assoc()
  #                           instead of an array.
  #
  #   returns mixed - If you are not connected to a MySQL Server
  #                   you will get a false. If you set $return_resource
  #                   to true, you will receive a resource, otherwise an
  #                   array.
  #
  public function variables($return_resource = false)
  {
    if($this->db_con)
    {
      # Query the server for the variables.
      $db_result = $this->query('SHOW VARIABLES');

      # Want an array instead? Good.
      if(!$return_resource && $db_result)
      {
        # Setup a temporary array.
        $temp = array();

        # Loop through the result.
        while($row = $this->fetch_row($db_result))
          # Add it to the array.
          $temp[$row[0]] = $row[1];

        # Now that we are done with the result
        # set db_result to the array for returning.
        $db_result = $temp;
      }

      # Return the db_result, array or not ;)
      return $db_result;
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }

  #
  # Returns the MySQL Thread ID.
  # int MySQL::thread_id();
  #   returns int - Returns the Thread ID of the MySQL Server.
  #                 On failure, you get false.
  #
  public function thread_id()
  {
    if($this->db_con)
    {
      # Just return the Thread ID.
      return @mysql_thread_id($this->db_con);
    }
    else
    {
      trigger_error('Must be connected to a MySQL Server', E_USER_NOTICE);
      return false;
    }
  }
  
  /**
   * Override fetch_assoc method by fetchByAssoc
   * @author Satish Gupta
   */
  
  public function fetchByAssoc($db_result){
  	return $this->fetch_assoc($db_result);
  }
}
?>
