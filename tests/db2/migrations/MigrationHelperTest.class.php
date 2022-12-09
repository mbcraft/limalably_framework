<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class MigrationHelperTest extends LTestCase {
	


	function testMigrationTableCreation() {

		LMigrationHelper::dropMigrationTable();

		$this->assertFalse(LMigrationHelper::migrationTableExists(),"La tabella delle migrazioni esiste dopo il drop!");

		LMigrationHelper::ensureMigrationTableExist();

		$this->assertTrue(LMigrationHelper::migrationTableExists(),"La tabella delle migrazioni non esiste dopo il create!");

		LMigrationHelper::dropMigrationTable();

		$this->assertFalse(LMigrationHelper::migrationTableExists(),"La tabella delle migrazioni esiste dopo il drop!");

	}

	function testMigrationMethods() {

		LMigrationHelper::ensureMigrationTableExist();

		$this->assertFalse(LMigrationHelper::isMigrationExecuted('my_name','my_context'),"E' stata trovata una migrazione che non dovrebbe esistere!");

		LMigrationHelper::logMigration('my_name','my_context');

		$this->assertTrue(LMigrationHelper::isMigrationExecuted('my_name','my_context'),"E' stata trovata una migrazione che non dovrebbe esistere!");

		$executed_at = LMigrationHelper::getMigrationExecutionTime('my_name','my_context');

		$this->assertTrue(strlen($executed_at)>5,"Il tempo della migrazione non è corretto!");

		LMigrationHelper::removeMigrationLog('my_name','my_context');

		$this->assertFalse(LMigrationHelper::isMigrationExecuted('my_name','my_context'),"E' stata trovata una migrazione che non dovrebbe esistere!");

		$this->assertFalse(LMigrationHelper::getMigrationExecutionTime('my_name','my_context'),"Il metodo del tempo di esecuzione non ritorna false quando la migrazione non esiste!");

		LMigrationHelper::dropMigrationTable();

	}

}