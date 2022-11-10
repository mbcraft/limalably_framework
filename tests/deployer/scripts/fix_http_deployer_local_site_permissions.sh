#
# Author : MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#

echo "Fixing local http deployer test website permissions ..."

#sudo chown www-data /home/marco/PhpProjects/DeployerTestLocalSite/deployer.php
sudo chgrp www-data /home/marco/PhpProjects/DeployerTestLocalSite/deployer.php
