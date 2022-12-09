<?php



class LDbUtils {
	

	public static function deleteAllTables($connection_name = null) {

		$db = db($connection_name);

		foreign_key_checks(false)->go($db);

		$table_list = table_list()->go($db);

        foreach ($table_list as $tb) {
            drop_table($tb)->go($db);
        }

        foreign_key_checks(true)->go($db);
	}

	public static function listAllConnections() {

        $db_list = LConfigReader::simple('/database');

        $result_data = [];

        foreach ($db_list as $db_name => $db_data) {
            $result_data[] = $db_name;
        }

        return $result_data;

	}

	public static function listAllTables($connection_name) {

		$db = db($connection_name);

		$result = table_list()->go($db);

		return $result;

	}

	public static function executeSqlFilesZip($connection_name,$zip_path) {

		$db = db($connection_name);

		$f = new LFile($zip_path);

		if (!$f->exists()) return "Zip archive not found!";

		if (!$f->isReadable()) return "Zip archive not readable!";

		$random_dir_name = LRandomUtils::letterCode(10);

		$extraction_dir = new LDir("temp/".$random_dir_name."/");
		$extraction_dir->touch();

		if (!$extraction_dir->exists()) return "Unable to create extraction dir!";

		LZipUtils::expandArchive($f,$extraction_dir);

		$sql_files_list = $extraction_dir->listFiles();

		foreign_key_checks(false)->go($db);

		foreach ($sql_files_list as $sql_file) {
			query_list_from_file($sql_file)->go($db);
		}

		foreign_key_checks(true)->go($db);

		$extraction_dir->delete(true);

		return true;
		
	}

}