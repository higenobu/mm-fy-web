#!/bin/bash

BASE_URL="http://localhost:8000"
USERNAME="testuser"
PASSWORD="password123"

echo "🧪 Complete System Test"
echo "======================="
echo ""

# Test 1: Home page loads
echo "Test 1: Home page (not logged in)"
echo "---------------------------------"
response=$(curl -s "$BASE_URL/")
if echo "$response" | grep -qi "Welcome to MM-FY\|MM-FY Analysis"; then
    echo "✅ Home page loads"
else
    echo "❌ Home page failed"
    echo "   Debug: Looking for content..."
    echo "$response" | grep -i "welcome\|mm-fy" | head -3
fi
echo ""

# Test 2: Login page loads
echo "Test 2: Login page loads"
echo "------------------------"
response=$(curl -s "$BASE_URL/login.php")
if echo "$response" | grep -qi "Sign In\|Login"; then
    echo "✅ Login page loads"
else
    echo "❌ Login page failed"
fi
echo ""

# Test 3: Login with credentials
echo "Test 3: Login with credentials"
echo "------------------------------"
response=$(curl -s -c cookies.txt -L -X POST "$BASE_URL/login.php" \
    -d "username=$USERNAME" \
    -d "password=$PASSWORD")
if echo "$response" | grep -qi "Dashboard\|Welcome.*testuser\|menu-grid"; then
    echo "✅ Login successful (redirected to dashboard)"
else
    echo "⚠️  Login response received (checking session...)"
    # Check if session was actually created
    session_check=$(curl -s -b cookies.txt "$BASE_URL/api/session.php")
    if echo "$session_check" | jq -e '.authenticated' 2>/dev/null | grep -q "true"; then
        echo "✅ Login successful (session active)"
    else
        echo "❌ Login failed"
    fi
fi
echo ""

# Test 4: Session check
echo "Test 4: Session check (authenticated)"
echo "-------------------------------------"
response=$(curl -s -b cookies.txt "$BASE_URL/api/session.php")
if echo "$response" | jq -e '.authenticated' 2>/dev/null | grep -q "true"; then
    echo "✅ Session active"
    username=$(echo "$response" | jq -r '.user.username' 2>/dev/null)
    echo "   User: $username"
else
    echo "❌ Session not active"
    echo "   Response: $response"
fi
echo ""

# Test 5: Access protected API
echo "Test 5: Access protected API"
echo "----------------------------"
response=$(curl -s -b cookies.txt "$BASE_URL/api/japanese_fy_results.php")
if echo "$response" | jq -e '.success' 2>/dev/null | grep -q "true"; then
    echo "✅ API access granted"
    count=$(echo "$response" | jq -r '.count' 2>/dev/null)
    user_id=$(echo "$response" | jq -r '.user_id' 2>/dev/null)
    echo "   Records: $count"
    echo "   User ID: $user_id"
else
    echo "❌ API access denied"
    echo "   Response: $response" | head -3
fi
echo ""

# Test 6: Logout
echo "Test 6: Logout"
echo "-------------"
curl -s -b cookies.txt "$BASE_URL/logout.php" > /dev/null
response=$(curl -s -b cookies.txt "$BASE_URL/api/session.php")
if echo "$response" | jq -e '.authenticated' 2>/dev/null | grep -q "false"; then
    echo "✅ Logout successful"
else
    echo "❌ Logout failed"
fi

# Cleanup
rm -f cookies.txt

echo ""
echo "========================================="
echo "✅ Complete System Test Finished"
echo "========================================="
