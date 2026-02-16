#!/bin/bash

BASE_URL="${BASE_URL:-http://localhost:8000}"
USERNAME="${USERNAME:-testuser}"
PASSWORD="${PASSWORD:-password123}"

echo "🔒 Testing Access Control"
echo "========================="
echo "Base URL: $BASE_URL"
echo "Username: $USERNAME"
echo ""

# Cleanup previous cookies
rm -f cookies.txt

# Test 1: Unauthenticated request (should fail)
echo "Test 1: Unauthenticated access"
echo "------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/api/japanese_fy_results.php")
http_code=$(echo "$response" | tail -n1)
body=$(echo "$response" | head -n -1)

if [ "$http_code" = "401" ]; then
    echo "✅ PASS: Unauthenticated request blocked (HTTP 401)"
else
    echo "❌ FAIL: Expected HTTP 401, got $http_code"
    echo "Response: $body"
fi

echo ""

# Test 2: Check session before login
echo "Test 2: Check session (not logged in)"
echo "-------------------------------------"
session_check=$(curl -s "$BASE_URL/api/session.php")
if echo "$session_check" | jq -e '.authenticated' | grep -q "false"; then
    echo "✅ PASS: Session check returns not authenticated"
else
    echo "❌ FAIL: Session check failed"
    echo "$session_check" | jq '.'
fi

echo ""

# Test 3: Login to get session
echo "Test 3: Login with credentials"
echo "------------------------------"
login_response=$(curl -s -c cookies.txt -X POST \
    -H "Content-Type: application/json" \
    -d "{\"username\":\"$USERNAME\",\"password\":\"$PASSWORD\"}" \
    "$BASE_URL/api/login.php")

if echo "$login_response" | jq -e '.success' | grep -q "true"; then
    echo "✅ PASS: Login successful"
    user_id=$(echo "$login_response" | jq -r '.user.id')
    username=$(echo "$login_response" | jq -r '.user.username')
    patient_ids=$(echo "$login_response" | jq -r '.accessible_patients | join(", ")')
    echo "   User ID: $user_id"
    echo "   Username: $username"
    echo "   Accessible patients: $patient_ids"
else
    echo "❌ FAIL: Login failed"
    echo "$login_response" | jq '.'
    exit 1
fi

echo ""

# Test 4: Check session after login
echo "Test 4: Check session (logged in)"
echo "---------------------------------"
session_check=$(curl -s -b cookies.txt "$BASE_URL/api/session.php")
if echo "$session_check" | jq -e '.authenticated' | grep -q "true"; then
    echo "✅ PASS: Session check returns authenticated"
    echo "   User: $(echo "$session_check" | jq -r '.user.username')"
    echo "   Session expires in: $(echo "$session_check" | jq -r '.session_expires_in') seconds"
else
    echo "❌ FAIL: Session check failed"
    echo "$session_check" | jq '.'
fi

echo ""

# Test 5: Authenticated request (should succeed)
echo "Test 5: Access own records (authenticated)"
echo "-----------------------------------------"
response=$(curl -s -b cookies.txt "$BASE_URL/api/japanese_fy_results.php")

if echo "$response" | jq -e '.success' | grep -q "true"; then
    echo "✅ PASS: Authenticated request succeeded"
    user_id=$(echo "$response" | jq -r '.user_id')
    patient_ids=$(echo "$response" | jq -r '.accessible_patients | join(", ")')
    count=$(echo "$response" | jq -r '.count')
    echo "   User ID: $user_id"
    echo "   Accessible patients: $patient_ids"
    echo "   Records found: $count"
else
    echo "❌ FAIL: Authenticated request failed"
    echo "$response" | jq '.'
fi

echo ""

# Test 6: Try to access specific patient record
echo "Test 6: Access detailed patient record"
echo "--------------------------------------"
# Get first accessible patient ID
first_patient=$(echo "$login_response" | jq -r '.accessible_patients[0]')

if [ "$first_patient" != "null" ] && [ -n "$first_patient" ]; then
    response=$(curl -s -b cookies.txt \
        "$BASE_URL/api/japanese_fy_results_detailed.php?patient_id=$first_patient")
    
    if echo "$response" | jq -e '.success' | grep -q "true"; then
        echo "✅ PASS: Can access authorized patient record $first_patient"
        count=$(echo "$response" | jq -r '.count')
        echo "   Records found: $count"
    else
        echo "❌ FAIL: Cannot access authorized patient"
        echo "$response" | jq '.error'
    fi
else
    echo "⚠️  SKIP: No accessible patients found"
fi

echo ""

# Test 7: Try to access unauthorized patient record
echo "Test 7: Try accessing unauthorized patient record"
echo "-------------------------------------------------"
unauthorized_patient=9999  # Assuming this patient ID doesn't exist or user has no access

response=$(curl -s -b cookies.txt \
    "$BASE_URL/api/japanese_fy_results_detailed.php?patient_id=$unauthorized_patient")

if echo "$response" | jq -e '.error' | grep -qi "access denied\|not found"; then
    echo "✅ PASS: Access to unauthorized patient blocked"
else
    success=$(echo "$response" | jq -r '.success')
    if [ "$success" = "true" ]; then
        count=$(echo "$response" | jq -r '.count')
        if [ "$count" = "0" ]; then
            echo "✅ PASS: No records returned for unauthorized patient"
        else
            echo "❌ FAIL: Should have blocked access to patient $unauthorized_patient"
            echo "$response" | jq '.'
        fi
    else
        echo "✅ PASS: Access denied"
    fi
fi

echo ""

# Test 8: Logout
echo "Test 8: Logout"
echo "-------------"
logout_response=$(curl -s -b cookies.txt -X POST "$BASE_URL/api/logout.php")

if echo "$logout_response" | jq -e '.success' | grep -q "true"; then
    echo "✅ PASS: Logout successful"
else
    echo "❌ FAIL: Logout failed"
    echo "$logout_response" | jq '.'
fi

echo ""

# Test 9: Verify session is destroyed
echo "Test 9: Verify session destroyed after logout"
echo "---------------------------------------------"
response=$(curl -s -b cookies.txt "$BASE_URL/api/japanese_fy_results.php")
http_code=$(curl -s -o /dev/null -w "%{http_code}" -b cookies.txt "$BASE_URL/api/japanese_fy_results.php")

if [ "$http_code" = "401" ]; then
    echo "✅ PASS: Cannot access API after logout (HTTP 401)"
else
    echo "❌ FAIL: Expected HTTP 401 after logout, got $http_code"
fi

# Cleanup
rm -f cookies.txt

echo ""
echo "========================================="
echo "✅ Access Control Tests Complete"
echo "========================================="
