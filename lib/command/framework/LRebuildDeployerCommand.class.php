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
        $deployer_parts[] = 'tools/deployer_parts/DIOException.class.php'; //2
        $deployer_parts[] = 'tools/deployer_parts/DFileSystemElement.class.php'; //3
        $deployer_parts[] = 'tools/deployer_parts/DFileSystemUtils.class.php'; //4
        $deployer_parts[] = 'tools/deployer_parts/DDir.class.php'; //5
        $deployer_parts[] = 'tools/deployer_parts/DFile.class.php'; //6
        $deployer_parts[] = 'tools/deployer_parts/DFileReader.class.php'; //7
        $deployer_parts[] = 'tools/deployer_parts/DFileWriter.class.php'; //8
        $deployer_parts[] = 'tools/deployer_parts/DZipUtils.class.php'; //9
        $deployer_parts[] = 'tools/deployer_parts/DStringUtils.class.php'; //10
        $deployer_parts[] = 'tools/deployer_parts/DIInspector.interface.php'; //11
        $deployer_parts[] = 'tools/deployer_parts/DContentHashInspector.class.php'; //12
        $deployer_parts[] = 'tools/deployer_parts/DPermissionsFixerInspector.class.php'; //13
        $deployer_parts[] = 'tools/deployer_parts/DeployerController.class.php'; //14
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