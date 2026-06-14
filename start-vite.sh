#!/bin/bash

# Install npm dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

# Start Vite dev server
echo "Starting Vite dev server..."
npm run dev -- --host 0.0.0.0