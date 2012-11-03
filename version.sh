#!/bin/bash

git rev-parse HEAD | cut -c -7 > version.txt
