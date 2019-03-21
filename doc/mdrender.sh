#!/bin/bash
FILE=$1
F=${FILE%%.*}
node /usr/bin/showdown makehtml -i $1 -o $1.html
cat mdpre.html $1.html mdpost.html > $F.html
rm $1.html

