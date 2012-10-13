#!/bin/bash

cd tests
phpunit --colors --debug --verbose --coverage-html ./report $@
cd -
