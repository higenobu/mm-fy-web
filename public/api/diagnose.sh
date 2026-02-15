#!/bin/bash

echo "🔍 API Diagnostics"
echo "=================="
echo ""

cd ~/mm-fy-web

# Test 1: Simple response
echo "Test 1: Simple PHP response"
echo "---------------------------"
result=$(curl -s http://localhost:8000/api/test_simple.php)
if [ -n "$result" ]; then
    echo "✅ Got response: $result"
else
    echo "❌ Empty response"
fi
echo ""

# Test 2: Check error display
echo "Test 2: PHP info"
echo "----------------"
curl -s http://localhost:8000/api/test_errors.php | jq -r '.php_version // "Error"'
echo ""

# Test 3: Database connection
echo "Test 3: Database connection"
echo "---------------------------"
result=$(curl -s http://localhost:8000/api/test_db.php)
if echo "$result" | jq -e '.success' > /dev/null 2>&1; then
    echo "✅ Database connected"
    echo "$result" | jq -C '.'
else
    echo "❌ Database error"
    echo "$result" | jq -C '.'
fi
echo ""

# Test 4: Main API
echo "Test 4: Main API"
echo "----------------"
result=$(curl -s http://localhost:8000/api/japanese_fy_results.php)
if [ -z "$result" ]; then
    echo "❌ Empty response - check server logs"
elif echo "$result" | jq -e '.success' > /dev/null 2>&1; then
    echo "✅ API works!"
    echo "$result" | jq -C '. | {success, count}'
else
    echo "❌ API error:"
    echo "$result" | jq -C '.'
fi

echo ""
echo "Done!"
