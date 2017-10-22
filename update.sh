#!/bin/sh

# This file updates the bot to the latest version automatically.
# Your config will not be lost

# keep files
git update-index --assume-unchanged config.php
git update-index --assume-unchanged chrisify/
git update-index --assume-unchanged bot_modules/prio2/haffkrug.php
git update-index --assume-unchanged bot_modules/prio1/03_lachsticker.php

# delete lock file to avoid problems
# don't worry, we will overwrite everything anyway...
rm ./.git/index.lock

# update from repo
git stash
git pull
git stash pop

# run updatescripts for further action
php update_scripts/run.php
