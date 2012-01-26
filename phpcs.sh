#!/bin/bash

phpcs --standard=Zend `find application/ library/DZend/ library/LightningPackerHelper/ | grep "\.php"` | less
