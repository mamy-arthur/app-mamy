#!/bin/bash

export UID=$(id -u)
export GID=$(id -g)
chown -R $UID ./

set -e

if command -v docker-compose > /dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker-compose"
elif command -v docker > /dev/null 2>&1 && docker compose version > /dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker compose"
else
    exit 1
fi

if [[ $1 = 'up:local' ]]; then
  shift
	$DOCKER_COMPOSE_CMD -f docker-compose.yml.local up "$@"
elif [[ $1 = 'up' ]]; then
  shift
  $DOCKER_COMPOSE_CMD up "$@"
elif [[ $1 = 'down' ]]; then
  shift
  $DOCKER_COMPOSE_CMD down "$@"
elif [[ $1 = 'logs' ]]; then
  shift
  $DOCKER_COMPOSE_CMD logs "$@"
elif [[ $1 = 'deploy' ]]; then
  shift
  $DOCKER_COMPOSE_CMD up --build -d
  $DOCKER_COMPOSE_CMD logs --tail="all" -f
elif [[ $1 = 'exec' ]]; then
  shift
  if [[ $1 = 'api' ]]; then
    shift
    if [[ $1 = 'all' ]]; then
      APIS=$(find . -maxdepth 1 -name "*_api" -type d -exec basename {} \; | sed "s/_api//g")

      for app in $APIS
      do
        echo "$app API =>"
        docker exec -t "app_${app}_api_php" bash -c "${@:2}"
      done
    else
      echo "$1 API =>"
      docker exec -t "mosolf_$1_api_php" bash -c "${@:2}"
    fi
  elif [[ $1 = 'front' ]]; then
    echo "$1 container =>"
    docker exec -t "app_front_nodejs" bash -c "${@:2}"
  elif [[ $1 = 'db' ]]; then
    echo "$1 container =>"
    docker exec -t "app_db_postgres" bash -c "${@:2}"
  else
    echo "$1 container =>"
    docker exec -t "app_$1" bash -c "${@:2}"
  fi
fi
