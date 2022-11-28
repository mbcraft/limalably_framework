#!/bin/bash

#
# @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#
#
#

# Some help from :
# https://stackoverflow.com/questions/59895/get-the-source-directory-of-a-bash-script-from-within-the-script-itself?page=1&tab=votes#tab-top
# https://stackoverflow.com/users/407731
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PARENT_DIR="$(dirname $DIR)"

php $PARENT_DIR/init.php project/get_execution_mode
