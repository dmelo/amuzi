#!/bin/bash

cd tests
# phpunit --colors --debug --verbose --coverage-html ./report $@
phpunit --colors --debug --verbose $@
cd -
