#!/bin/bash
CURR_DIR=$PWD;
TARGET_DIR="$PWD/$2";
while IFS='' read -r line || [[ -n "$line" ]]; do
	test -d "$TARGET_DIR" || mkdir -p "$TARGET_DIR" && cp --parents $line "$TARGET_DIR"
done < "$1"
cp -a --parents ./skin/frontend/default/default/storelocator/images "$2"
cp -a --parents ./skin/frontend/rwd/default/storelocator/images "$2"
cp -a --parents ./skin/frontend/rwd/default/storelocator/fonts "$2"
cp -a ./read_xls "$2"
cp -a --parents ./media/storelocator "$2"
