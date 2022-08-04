#!/bin/bash

# Some help from :
# https://stackoverflow.com/questions/59895/get-the-source-directory-of-a-bash-script-from-within-the-script-itself?page=1&tab=votes#tab-top
# https://stackoverflow.com/users/407731
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PARENT_DIR="$(dirname $DIR)"

cd $PARENT_DIR

cp -r project_image ..
mv ../project_image ../$1

echo "Project '"$1"' created successfully."
echo "Remember to correct the framework folder in project init.php file."
echo "Then run 'composer update' to install the required framework dependencies."
