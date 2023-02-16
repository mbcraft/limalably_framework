<?php



class LProjectMigrateCommand implements LICommand {

	private function help() {

		LResult::messagenl("Migrate command help :");
		LResult::messagenl("");
		LResult::messagenl("./bin/migrate.sh all --> executes all missing migration on this running mode");
		LResult::messagenl("./bin/migrate.sh reset --> cleans up the main db and all executed migration on this running mode");
		LResult::messagenl("./bin/migrate.sh list_done --> prints all executed migrations on this running mode");
		LResult::messagenl("./bin/migrate.sh list_missing --> prints all missing migrations on this running mode");
		LResult::messagenl("");
			
	}

	private function all() {
		LMigrationSupport::executeAllMigrations();
	}

	private function reset() {
		LMigrationSupport::resetAllMigrations();
	}

	private function list_done() {
		LMigrationSupport::printAllExecutedMigrations();
	}

	private function list_missing() {
		LMigrationSupport::printAllMissingMigrations();
	}
	

	public function execute() {
	

        $parameter_map = [
            'help' => 1,
            'all' => 2,
            'reset' => 2,
            'list_done' => 2,
            'list_missing' => 2
            //missing the command for modules tailored migration execution and revert ... 
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

        switch ($command) {

        	case 'all' : $this->all(); break;
        	case 'reset' : $this->reset(); break;
        	case 'list_done': $this->list_done(); break;
        	case 'list_missing': $this->list_missing(); break;

        }
	}


}