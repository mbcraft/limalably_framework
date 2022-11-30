#
# Author : MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#

echo "Fixing local http deployer test website permissions ..."

sudo chown -R www-data /home/marco/PhpProjects/DeployerTestLocalSite/*
sudo chgrp -R www-data /home/marco/PhpProjects/DeployerTestLocalSite/*
