#!/bin/bash

MODEL=application/models/

cp scripts/model.php ${MODEL}${1}.php
cp scripts/modelDb.php ${MODEL}DbTable/${1}.php
cp scripts/modelDbRow.php ${MODEL}DbTable/${1}Row.php

sed -i "s/CLASSNAME/${1}/g" ${MODEL}${1}.php ${MODEL}DbTable/${1}.php ${MODEL}DbTable/${1}Row.php
