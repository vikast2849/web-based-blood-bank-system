#!/usr/bin/env bash
# Local server for macOS/Linux. Needs PHP (with mysqli) and a running MySQL/MariaDB.
cd "$(dirname "$0")"
PORT=${PORT:-8000}
php tools/setup_db.php || exit 1
echo "Blood Bank running at http://localhost:$PORT/  (Ctrl+C to stop)"
exec php -S "localhost:$PORT" -t src
