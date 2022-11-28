#!/bin/bash

#
# @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
#
#
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PARENT_DIR="$(dirname $DIR)"

php $PARENT_DIR/init.php project/deployer $1 $2 $3 $4 $5