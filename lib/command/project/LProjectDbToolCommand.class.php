<?php



class LProjectDbToolCommand implements LICommand {
	
	private function help() {

		LResult::messagenl("Db tool command help :");
		LResult::messagenl("");
		LResult::messagenl("./bin/db_tool.sh help --> prints this help");
		LResult::messagenl("./bin/db_tool.sh list_connections --> lists all available connections");
		LResult::messagenl("./bin/db_tool.sh reset_tables <connection_name> --> resets all tables using the specified connection");
		LResult::messagenl("./bin/db_tool.sh list_tables <connection_name> --> lists all the tables using the specified connection");
		LResult::messagenl("./bin/db_tool.sh run_sql_files_zip <connection_name> <zip_path> --> executes all the sql files found in zip archive using the specified connection");
		
		LResult::messagenl("");
			
	}

	private function list_connections() {
		$connections_list = LDbUtils::listAllConnections();

		LResult::messagenl("Connections list set up for this project :");

		foreach ($connections_list as $conn_name) {
			LResult::messagenl("	- ".$conn_name);
		}
	}

	private function reset_tables(string $connection_name) {
		LDbUtils::deleteAllTables($connection_name);

		LResult::messagenl("Tables reset executed successfully for connection (".$connection_name.").");
	}

	private function list_tables(string $connection_name) {
		$tables_list = LDbUtils::listAllTables($connection_name);

		if (count($tables_list)==0) {

			LResult::messagenl("No tables found using this connection (".$connection_name.").");
			return;

		} else {

			LResult::messagenl("Tables list for this connection (".$connection_name.") :");

			foreach ($tables_list as $tb_name) {
				LResult::messagenl("	- ".$tb_name);
			}

		}
	}

	private function run_sql_files_zip(string $connection_name,string $zip_path) {
		$result = LDbUtils::executeSqlFilesZip($connection_name,$zip_path);

		if ($result===true)
			LResult::messagenl("Sql files runned successfully for this connection (".$connection_name.").");
		else {
			LResult::messagenl("Error during sql zip file extraction : ");
			LResult::messagenl($result);
		}
	}

	public function execute() {
		$parameter_map = [
            'help' => 1,
            'list_connections' => 1,
            'reset_tables' => 2,
            'list_tables' => 2,
            'run_sql_files_zip' => 3
        ];

        if (LParameters::count()<1) {
            $this->help();
            return;
        }

        if (LParameters::count()==1 && !isset($parameter_map[LParameters::getByIndex(0)])) {
            echo "Unknown command '".LParameters::getByIndex(0)."'.\n";
            $this->help();
            return;
        }

        $command = LParameters::getByIndex(0);

        if (LParameters::count()>1) {
        	$connection_name = LParameters::getByIndex(1);
    	}

    	if (LParameters::count()>2) {
        	$zip_path = LParameters::getByIndex(2);
    	}

        switch ($command) {

        	case 'help' : $this->help(); break;
        	case 'list_connections' : $this->list_connections(); break;
        	case 'reset_tables': $this->reset_tables($connection_name); break;
        	case 'list_tables': $this->list_tables($connection_name); break;
        	case 'run_sql_files_zip' : $this->run_sql_files_zip($connection_name,$zip_path);break;

        }
	}

}