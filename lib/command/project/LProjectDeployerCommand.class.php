<?php


class LProjectDeployerCommand implements LICommand {
	

	public function execute() {

        $dc = new LDeployerClient();

        $parameter_map = [
            'help' => 1,
            'attach' => 4,
            'detach' => 2,
            'set_deployer_path_from_root' => 3,
            'get_deployer_path_from_root' => 2,
            'add_ignore' => 3,
            'rm_ignore' => 3,
            'print_ignore' => 2,
            'deployer_version' => 2, 
            'deployer_update' => 2,
            'framework_check' => 2,
            'framework_update' => 2,
            'project_check' => 2,
            'project_update' => 2,
            'auto_config' => 2,
            'manual_config' => 3,
            'backup' => 4,
            'disappear' => 2,
            'reset' => 2,
            'temp_clean' => 2,
            'get_exec_mode' => 2,
            'set_exec_mode' => 3,
            'list_db' => 2,
            'backup_db_structure' => 4,
            'backup_db_data' => 4,
            'migrate_all' => 2,
            'migrate_reset' => 2,
            'migrate_list_done' => 2,
            'migrate_list_missing' => 2
        ];

        if (LParameters::count()<1) {
            $dc->help();
            return;
        }

        if (LParameters::count()==1) {
            echo "Unknown command '".LParameters::getByIndex(0)."'.\n";
            $dc->help();
            return;
        }

        $deploy_key_name = LParameters::getByIndex(0);

        $command = LParameters::getByIndex(1);

        if (!isset($parameter_map[$command])) {
            echo "Unknown command.\n";
            $dc->help();
            return;
        }

        if (isset($parameter_map[$command]) && $parameter_map[$command]!=LParameters::count()) {
            echo "Parameter number mismatch : found ".LParameters::count()." parameters.\n";
            $dc->help();
            return;
        }

        if (LParameters::count()>2) {
            $parameter2 = LParameters::getByIndex(2);
        }
        if (LParameters::count()>3) {
            $parameter3 = LParameters::getByIndex(3);
        }

        switch ($command) {
            case 'help' : $dc->help();break;
            case 'attach': $dc->attach($deploy_key_name,$parameter2,$parameter3);break;
            case 'detach' : $dc->detach($deploy_key_name);break;
            case 'get_deployer_path_from_root' : $dc->get_deployer_path_from_root($deploy_key_name);break;
            case 'set_deployer_path_from_root' : $dc->set_deployer_path_from_root($deploy_key_name,$parameter2);break;
            case 'add_ignore' : $dc->add_to_ignore_list($deploy_key_name,$parameter2); break;
            case 'rm_ignore' : $dc->rm_from_ignore_list($deploy_key_name,$parameter2); break;
            case 'print_ignore' : $dc->print_ignore_list($deploy_key_name); break;
            case 'deployer_version' : $dc->deployer_version($deploy_key_name);break;
            case 'deployer_update' : $dc->deployer_update($deploy_key_name);break;
            case 'framework_check' : $dc->framework_check($deploy_key_name);break;
            case 'framework_update' : $dc->framework_update($deploy_key_name);break;
            case 'project_check' : $dc->project_check($deploy_key_name);break;
            case 'project_update' : $dc->project_update($deploy_key_name);break;
            case 'auto_config' : $dc->auto_config($deploy_key_name);break;
            case 'manual_config' : $dc->manual_config($deploy_key_name,$parameter2);break;
            case 'backup' : $dc->backup($deploy_key_name,$parameter2,$parameter3);break;
            case 'disappear' : $dc->disappear($deploy_key_name);break;
            case 'reset' : $dc->reset($deploy_key_name);break;
            case 'temp_clean': $dc->temp_clean($deploy_key_name);break;
            case 'get_exec_mode': $dc->get_exec_mode($deploy_key_name);break;
            case 'set_exec_mode': $dc->set_exec_mode($deploy_key_name,$parameter2);break;
            case 'list_db': $dc->list_db($deploy_key_name);break;
            case 'backup_db_structure': $dc->backup_db_structure($deploy_key_name,$parameter2,$parameter3);break;
            case 'backup_db_data': $dc->backup_db_data($deploy_key_name,$parameter2,$parameter3);break;
            case 'migrate_all' : $dc->migrate_all($deploy_key_name);break;
            case 'migrate_reset': $dc->migrate_reset($deploy_key_name);break;
            case 'migrate_list_done': $dc->migrate_list_done($deploy_key_name);break;
            case 'migrate_list_missing': $dc->migrate_list_missing($deploy_key_name);break;

            default : throw new \Exception("Command handler not implemented : ".$command);

        }

        return;
    }


}