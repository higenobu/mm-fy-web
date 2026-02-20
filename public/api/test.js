echo ""
echo "Test Suite 5: Frontend Pages"
echo "-----------------------------"

# Test personality profile page
profile_code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/personality_profile.html?patient_id=$PATIENT_ID")
if [ "$profile_code" = "200" ]; then
    test_pass "Personality profile page accessible"
else
    test_fail "Personality profile page accessible (HTTP $profile_code)"
fi

# Test historical graph page
history_code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/historical_graph.html?patient_id=$PATIENT_ID")
if [ "$history_code" = "200" ]; then
    test_pass "Historical graph page accessible"
else
    test_warn "Historical graph page accessible (HTTP $history_code)"
fi

# Test sentiment graph page
sentiment_code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/sentiment_graph.html?patient_id=$PATIENT_ID")
if [ "$sentiment_code" = "200" ]; then
    test_pass "Sentiment graph page accessible"
else
    test_warn "Sentiment graph page accessible (HTTP $sentiment_code)"
fi

