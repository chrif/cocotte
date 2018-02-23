#!/bin/sh


rm_cocotte() {
	docker-compose down -v --remove-orphans
}

init_cocotte() {

	rm_cocotte

	set +e
	docker network create traefik
	set -e

	docker volume create cocottelog
}

root_cmd() {
	docker exec cocottephp $@
}



is_empty() {
	local var=${1:-}

	[ -z "$var" ]
}


is_not_empty() {
	local var=${1:-}

	[ -n "$var" ]
}

error() {
	local message="${1}"
	local code="${2:-1}"

	if is_not_empty "$message"; then
		echo "Error: ${message}; Exiting with status ${code}"
	else
		echo "Error; Exiting with status ${code}"
	fi

	exit "${code}"
}

is_dir() {
	local directory=${1:-}

	[ -d "$directory" ]
}

is_not_dir() {
	local directory=${1:-}

	[ ! -d "$directory" ]
}

is_file() {
	local var=${1:-}

	[ -f "$var" ]
}

is_not_file() {
	local var=${1:-}

	[ ! -f "$var" ]
}


dir_resolve() {
	cd "$1" 2>/dev/null || return $?  # cd to desired directory; if fail, quell any error messages but return exit status
	echo "`pwd -P`" # output full, link-resolved path
}
