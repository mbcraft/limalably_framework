<?php

function db($connection_name = null) {
    return LDbConnectionManager::get($connection_name);
}