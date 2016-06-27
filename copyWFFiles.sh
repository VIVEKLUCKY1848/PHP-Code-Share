#!/bin/bash
CURR_DIR=$PWD;
TARGET_DIR="$PWD/$2";
while IFS='' read -r line || [[ -n "$line" ]]; do
	test -d "$TARGET_DIR" || mkdir -p "$TARGET_DIR" && cp --parents $line "$TARGET_DIR"
done < "$1"
