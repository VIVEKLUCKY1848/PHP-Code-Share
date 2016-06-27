#!/bin/bash
CURR_DIR=$PWD;
TARGET_DIR="$PWD/$2";
while IFS='' read -r line || [[ -n "$line" ]]; do
	if [[ ! "$line" =~ "~" && ! "$line" =~ "$1" ]]; then
		test -d "$TARGET_DIR" || mkdir -p "$TARGET_DIR" && cp --parents $line "$TARGET_DIR"
	fi
done < "$1"
