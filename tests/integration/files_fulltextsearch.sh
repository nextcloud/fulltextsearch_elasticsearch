#!/usr/bin/env bash
# SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
# SPDX-License-Identifier: AGPL-3.0-or-later
#
# End-to-end test of files_fulltextsearch with the Elasticsearch platform.
# Must be run from the server root of a Nextcloud instance served at NEXTCLOUD_URL.

set -euo pipefail

DATA_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/data"
NEXTCLOUD_URL="${NEXTCLOUD_URL:-http://localhost:8080}"
ELASTIC_HOST="${ELASTIC_HOST:-http://localhost:9200}"
export OC_PASS='fts-integration-password'

upload() {
	curl -fsS -u "$1:$OC_PASS" -T "$DATA_DIR/$2" "$NEXTCLOUD_URL/remote.php/dav/files/$1/$2"
}

delete() {
	curl -fsS -u "$1:$OC_PASS" -X DELETE "$NEXTCLOUD_URL/remote.php/dav/files/$1/$2"
}

share_with() {
	curl -fsS -u "$1:$OC_PASS" -H 'OCS-APIRequest: true' \
		-d "path=/$2" -d shareType=0 -d "shareWith=$3" \
		"$NEXTCLOUD_URL/ocs/v2.php/apps/files_sharing/api/v1/shares" > /dev/null
}

index() {
	./occ fulltextsearch:index --no-readline
	curl -fsS -X POST "$ELASTIC_HOST/_refresh" > /dev/null
}

# Prints the titles of the files matching the search for the given user
search() {
	./occ fulltextsearch:search --output=json "$1" "$2" | jq -r '(.files? // [])[].title'
}

# Succeeds when the search of the given user returns the given file name
has_result() {
	local titles
	titles=$(search "$1" "$2") || { echo "FAIL: search failed for $1"; exit 1; }
	grep -qE "(^|/)$3$" <<< "$titles"
}

expect_found() {
	if ! has_result "$1" "$2" "$3"; then
		echo "FAIL: $1 searching '$2' should find $3"
		exit 1
	fi
	echo "OK: $1 searching '$2' finds $3"
}

expect_not_found() {
	if has_result "$1" "$2" "$3"; then
		echo "FAIL: $1 searching '$2' should not find $3"
		exit 1
	fi
	echo "OK: $1 searching '$2' does not find $3"
}

./occ user:add --password-from-env alice
./occ user:add --password-from-env bob

upload alice xylophonist.txt
upload alice quokka.pdf
index

expect_found alice xylophonist xylophonist.txt
expect_found alice quokka quokka.pdf
expect_not_found alice marmalade xylophonist.txt
expect_not_found bob xylophonist xylophonist.txt

share_with alice xylophonist.txt bob
index

expect_found bob xylophonist xylophonist.txt
expect_not_found bob quokka quokka.pdf

delete alice quokka.pdf
index

expect_not_found alice quokka quokka.pdf
