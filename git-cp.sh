#!/bin/bash
# Target directory
TARGET=$1
echo "Coping to $TARGET"
MOST_RECENT=$(git log -n 1 --pretty=format:'%h')
PREV=$(git log --skip=1 -n 1 --pretty=format:'%h')

for i in $(git diff --name-only $MOST_RECENT $PREV)
do
# First create the target directory, if it doesn't exist.
mkdir -p "$TARGET/$(dirname $i)"
# Then copy over the file.
cp "$i" "$TARGET/$i"
done
echo "Done";

#example ./git-cp.sh cpDir