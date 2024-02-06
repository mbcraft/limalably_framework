#
# Author : MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#

echo "Deleting local http website deployer instance ...";

sudo rm -rf /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/*

mkdir /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/config/
mkdir /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/config/mode/
mkdir /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/config/deployer/
touch /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/config/mode/development.txt

sudo echo "marco" > /home/marco/SoftwareProjects/MBCRAFT/DeployerTestLocalSite/config/mode/development.txt


