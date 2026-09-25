#!/usr/bin/env bash
# ============================================================
# docker-run.sh — Helper script for Docker operations
# Usage: ./docker-run.sh [command]
# ============================================================
set -e

COMPOSE="docker compose"
SVC="app"

case "$1" in

  # ---------------------
  # Environment setup
  # ---------------------
  setup)
    if [ -f .env.docker ]; then
      echo "→ Copying .env.docker to .env..."
      cp -n .env.docker .env || true
    else
      echo "→ Copying .env.example to .env..."
      cp -n .env.example .env || true
    fi

    echo "→ Building image..."
    $COMPOSE build --no-cache

    echo "→ Starting container..."
    $COMPOSE up -d

    echo "→ Waiting for services to be ready..."
    sleep 10

    echo "→ Running migrations..."
    $COMPOSE exec $SVC php artisan migrate --force --seed

    echo "✓ Setup complete! App is running at http://localhost"
    ;;

  # ---------------------
  # Start
  # ---------------------
  up)
    $COMPOSE up -d
    ;;

  # ---------------------
  # Start with Vite dev server
  # ---------------------
  dev)
    $COMPOSE --profile dev up -d
    ;;

  # ---------------------
  # Stop
  # ---------------------
  down)
    $COMPOSE down
    ;;

  # ---------------------
  # Rebuild image
  # ---------------------
  build)
    $COMPOSE build "${@:2}"
    ;;

  # ---------------------
  # Artisan commands
  # ---------------------
  artisan)
    $COMPOSE exec $SVC php artisan "${@:2}"
    ;;

  # ---------------------
  # Composer commands
  # ---------------------
  composer)
    $COMPOSE exec $SVC composer "${@:2}"
    ;;

  # ---------------------
  # Shell
  # ---------------------
  shell)
    $COMPOSE exec $SVC sh
    ;;

  # ---------------------
  # Logs
  # ---------------------
  logs)
    $COMPOSE logs -f "${@:2}"
    ;;

  # ---------------------
  # Tests
  # ---------------------
  test)
    $COMPOSE exec $SVC php artisan test --compact "${@:2}"
    ;;

  # ---------------------
  # PHP Pint
  # ---------------------
  pint)
    $COMPOSE exec $SVC vendor/bin/pint "${@:2}"
    ;;

  # ---------------------
  # Full reset (remove volumes)
  # ---------------------
  reset)
    echo "⚠ This will destroy all data. Press Ctrl+C to cancel..."
    sleep 3
    $COMPOSE down -v
    echo "✓ Container and volumes removed."
    ;;

  *)
    echo "WebTopup Docker Helper"
    echo ""
    echo "Usage: ./docker-run.sh <command>"
    echo ""
    echo "Commands:"
    echo "  setup       First-time setup (copy .env, build, migrate)"
    echo "  up          Start container"
    echo "  dev         Start container with Vite dev server (profile dev)"
    echo "  down        Stop container"
    echo "  build       Rebuild Docker image"
    echo "  artisan     Run php artisan commands"
    echo "  composer    Run composer commands"
    echo "  shell       Open shell inside container"
    echo "  logs        Tail container logs"
    echo "  test        Run Pest tests"
    echo "  pint        Run PHP Pint linter"
    echo "  reset       Remove container and all volumes (destructive!)"
    ;;
esac
