#!/bin/bash

echo "🔧 Fixing autoload paths in all PHP files"
echo "=========================================="
echo ""

cd ~/mm-fy-web

# Function to calculate relative path
fix_file() {
    local file=$1
    local depth=$2
    local path_prefix=""
    
    for ((i=0; i<depth; i++)); do
        path_prefix="../${path_prefix}"
    done
    
    if [ -f "$file" ]; then
        # Check if file contains autoload
        if grep -q "require.*autoload.php" "$file"; then
            echo "Fixing: $file"
            echo "  Path: ${path_prefix}vendor/autoload.php"
            
            # Backup
            cp "$file" "${file}.backup"
            
            # Fix the path
            sed -i "s|require.*vendor/autoload.php.*;|require __DIR__ . '/${path_prefix}vendor/autoload.php';|g" "$file"
            
            # Verify
            grep "require.*autoload" "$file" | head -1
            echo ""
        fi
    fi
}

# Fix files in public/ (depth = 1, need ../)
echo "=== Files in public/ ==="
for file in public/*.php; do
    if [ -f "$file" ]; then
        fix_file "$file" 1
    fi
done

# Fix files in public/api/ (depth = 2, need ../../)
echo "=== Files in public/api/ ==="
for file in public/api/*.php; do
    if [ -f "$file" ]; then
        fix_file "$file" 2
    fi
done

# Fix files in root (depth = 0, no ../)
echo "=== Files in root ==="
for file in *.php; do
    if [ -f "$file" ]; then
        fix_file "$file" 0
    fi
done

echo ""
echo "✅ Done! All autoload paths fixed."
echo ""
echo "Restart your server:"
echo "  php -S localhost:8000 -t public/"
