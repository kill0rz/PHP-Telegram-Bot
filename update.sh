#!/bin/sh

git update-index --assume-unchanged config.php
git update-index --assume-unchanged chrisify/
git update-index --assume-unchanged bot_modules/prio2/haffkrug.php
git update-index --assume-unchanged bot_modules/prio1/03_lachsticker.php

#git fetch
#git checkout origin/master update.sh

git pull