#!/bin/bash

# Check if composer.lock exists; if not, create an empty one temporarily
if [ ! -f composer.lock ]; then
    echo "{}" > composer.lock
    TEMP_LOCK_FILE_CREATED=true
fi

# Build the Docker image
docker-compose build backend

# Remove the temporary composer.lock file if it was created
if [ "$TEMP_LOCK_FILE_CREATED" = true ]; then
    rm composer.lock
fi

