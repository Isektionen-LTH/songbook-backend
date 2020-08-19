#!/bin/sh

# Create database if not exists
echo "Creating database folder.."
mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
echo "DONE!"


# Start database server
echo "Starting database server.."
cd /usr
mysqld_safe --user=mysql --datadir=/var/lib/mysql > /dev/null &
echo "DONE!"

# Give database server some time to start up
echo "Waiting for database server to be ready"
sleep 5
echo "DONE!"


# Create database and user if not exists
echo "Creating database and user.."
echo "CREATE DATABASE IF NOT EXISTS songbook DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;" > /sql
echo "CREATE USER IF NOT EXISTS songbook_user@localhost IDENTIFIED BY 'songbook_password';" >> /sql
echo "GRANT ALL PRIVILEGES ON songbook.* TO songbook_user@localhost;" >> /sql
echo "FLUSH PRIVILEGES;" >> /sql
mysql < /sql
rm /sql
echo "DONE!"

# Install dependencies
echo "Installing dependencies.."
cd /app
composer install > /dev/null
echo "DONE!"

# Start apache
echo "Starting apache"
echo ""
echo ""
echo ""
echo ""
echo -e "\033[0;32m  ____  _____    _    ______   ___  \033[0m" 
echo -e "\033[0;32m |  _ \| ____|  / \  |  _ \ \ / / | \033[0m" 
echo -e "\033[0;32m | |_) |  _|   / _ \ | | | \ V /| | \033[0m" 
echo -e "\033[0;32m |  _ <| |___ / ___ \| |_| || | |_| \033[0m" 
echo -e "\033[0;32m |_| \_\_____/_/   \_\____/ |_| (_) \033[0m" 
echo ""
echo -e "\033[1;34mOpen http://songbook.local in your browser \033[0m"
echo -e "\033[1;34mPress Ctrl+C to shut down the server \033[0m"
httpd -D FOREGROUND &

# Prepare shutdown hook
shutdown_hook() {
  # Ignore multiple SIGINTS
  trap "" SIGINT

  # Shut down httpd and mysqld
  echo ""
  echo "Shutting down web server.."
  killall httpd
  echo "DONE!"
  echo "Shutting down database server.."
  killall mysqld_safe
  echo "DONE!"
  echo ""
  echo "Bye!"
  exit 0
}

# Attach shutdown hook
trap "shutdown_hook" SIGINT

# Wait for SIGINT
cat

