#!/bin/sh

git stash
git stash drop

git update-index --assume-unchanged config.php
git update-index --assume-unchanged chrisify/

git pull

#rights
chmod +x update.sh
