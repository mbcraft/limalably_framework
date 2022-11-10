#
# Author : MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#

echo "Fixing local http deployer test website permissions ..."

sudo chgrp www-data /home/marco/PhpProjects/DeployerTestLocalSite/deployer.php
