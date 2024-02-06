#
# Author : MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#

echo "Fixing local http deployer test website permissions ..."

sudo chown -R marco /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/*
sudo chgrp -R www-data /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/*

# Da capire se si può rimuovere in futuro ...

sudo chmod -R 775 /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/*   
