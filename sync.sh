#!/bin/bash
for dir in "api.tgm.pw" "docs.tik.bz" "expense.tik.bz" "ij.com" "ika.pw" "paste.tik.bz" "madhurendra.com" "tik.bz" "tikaj.com" "upadi-vet.com" "ontdot/ontdot.com"
do
	rsync -rvcupEH --existing /mnt/MAD/Dropbox/Projects/phpClasses/  /mnt/MAD/Dropbox/Projects/$dir/include
done
