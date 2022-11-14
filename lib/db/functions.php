<?php

function db($connection_name = 'default') {
    return LDbConnectionManager::get($connection_name);
}