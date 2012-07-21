#!/bin/bash

phpcs --standard=Zend `find application/ locale/ tests/ library/DZend/ library/LightningPackerHelper/ | grep "\.php$"` | less
