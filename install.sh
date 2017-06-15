#!/bin/sh
cwd=$(pwd)

#rights
chmod +x forcepull.sh

#gitignore
echo "config.php" >> .gitignore

#chrisify
sudo apt update && apt install -yy libopencv-dev golang
export PATH=$PATH:/usr/local/go/bin
mkdir ~/go
export GOPATH=~/go
export PATH=$PATH:$GOPATH/bin
go get github.com/zikes/chrisify
go get github.com/lazywei/go-opencv
cd $GOPATH/src/github.com/zikes/chrisify
go build
cd ..
cp -R chrisify/ $cwd/