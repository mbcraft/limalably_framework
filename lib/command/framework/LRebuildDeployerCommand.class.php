<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LRebuildDeployerCommand implements LICommand {
	

	public function execute() {

        echo "\n\nRebuilding deployer.php ...\n\n";
        
        $deployer_to_make = new LFile('tools/deployer_try.php');

        $deployer_parts = array();

        $deployer_parts[] = 'tools/deployer_parts/boot_functions.php'; //1
        $deployer_parts[] = 'tools/deployer_parts/LDIOException.class.php'; //2
        $deployer_parts[] = 'tools/deployer_parts/LDFileSystemElement.class.php'; //3
        $deployer_parts[] = 'tools/deployer_parts/LDFileSystemUtils.class.php'; //4
        $deployer_parts[] = 'tools/deployer_parts/LDDir.class.php'; //5
        $deployer_parts[] = 'tools/deployer_parts/LDFile.class.php'; //6
        $deployer_parts[] = 'tools/deployer_parts/LDFileReader.class.php'; //7
        $deployer_parts[] = 'tools/deployer_parts/LDFileWriter.class.php'; //8
        $deployer_parts[] = 'tools/deployer_parts/LDZipUtils.class.php'; //9
        $deployer_parts[] = 'tools/deployer_parts/LDStringUtils.class.php'; //10
        $deployer_parts[] = 'tools/deployer_parts/LDIInspector.interface.php'; //11
        $deployer_parts[] = 'tools/deployer_parts/LDContentHashInspector.class.php'; //12
        $deployer_parts[] = 'tools/deployer_parts/LDPermissionsFixerInspector.class.php'; //13
        $deployer_parts[] = 'tools/deployer_parts/LDeployerController.class.php'; //14
        $deployer_parts[] = 'tools/deployer_parts/deployer_start.php'; //15
        

        $deployer_content = "<?php\n\n";

        foreach ($deployer_parts as $part_path) {

            $f = new LFile($part_path);

            $content = $f->getContent();

            $lines = explode("\n",$content);

            unset($lines[0]);

            $content_without_starting_php = implode("\n",$lines);

            $deployer_content .= $content_without_starting_php;
        }

        $deployer_to_make->setContent($deployer_content);

        echo "Deployer rebuilt successfully!\n\n";

    }


}