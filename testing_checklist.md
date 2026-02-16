# 🧪 Manual Testing Checklist for Personality Profile Features

## ✅ Prerequisites
- [ ] Web server is running (Apache/Nginx or `php -S localhost:8000`)
- [ ] Database has test data for patient_id=2
- [ ] All files are in place (check with `ls -la public/api/`)
- [ ] Composer autoload is working (`composer dump-autoload`)

---

## 🔧 API Testing

### Test 1: Trait Info API
```bash
curl http://localhost:8000/api/trait_info.php | jq '.'
```

**Expected Result:**
```json
{
  "success": true,
  "traits": {
    "a_score": {
      "id": 1,
      "name_en": "Extraversion (Positive)",
      "name_ja": "外向性（積極的）",
      ...
    },
    ...
  },
  "description": "Big Five Personality Traits - Japanese 10-item inventory"
}
```

**Checklist:**
- [ ] Returns HTTP 200
- [ ] Valid JSON format
- [ ] Contains 10 traits (a, b, c, d, h, i, j, k, l, m)
- [ ] Each trait has: id, name_en, name_ja, description, dimension
- [ ] All 5 Big Five dimensions represented

---

### Test 2: Detailed Results API
```bash
curl "http://localhost:8000/api/japanese_fy_results_detailed.php?patient_id=2" | jq '.'
```

**Expected Result:**
```json
{
  "success": true,
  "count": 5,
  "data": [
    {
      "id": 123,
      "patient_id": 2,
      "text": "...",
      "a_score": 0.75,
      "b_score": 0.23,
      ...
      "personality_profile": [
        {
          "score_field": "a_score",
          "score_value": 0.75,
          "trait_name_ja": "外向性（積極的）",
          "level": "高い (High)",
          "interpretation": "社交的で活発、リーダーシップがある"
        },
        ...
      ],
      "big_five_summary": {
        "Extraversion": 0.76,
        "Neuroticism": 0.45,
        "Openness": 0.62,
        "Agreeableness": 0.58,
        "Conscientiousness": 0.71
      }
    }
  ]
}
```

**Checklist:**
- [ ] Returns HTTP 200
- [ ] `success` is `true`
- [ ] `count` matches number of records
- [ ] All 10 score fields present (a-d, h-m)
- [ ] `personality_profile` array has 10 entries
- [ ] Each profile entry has: score_field, score_value, trait_name_ja, level, interpretation
- [ ] `big_five_summary` has 5 dimensions
- [ ] All Big Five values are between 0 and 1

---

## 🌐 Frontend Testing

### Test 3: Personality Profile Page
Open: `http://localhost:8000/personality_profile.html?patient_id=2`

**Checklist:**
- [ ] Page loads without errors (check browser console)
- [ ] Patient ID is displayed correctly
- [ ] Data loads automatically on page load
- [ ] Loading spinner appears during data fetch
- [ ] All 10 score cards are displayed
- [ ] Big Five radar chart renders correctly
- [ ] Each score shows: name, value, level, interpretation
- [ ] Japanese text displays correctly (UTF-8)
- [ ] Page is responsive (test on mobile view)

---

### Test 4: Historical Graph Page
Open: `http://localhost:8000/historical_graph.html?patient_id=2`

**Checklist:**
- [ ] Page loads without errors
- [ ] "Load Data" button works
- [ ] Time series chart displays all scores
- [ ] Score checkboxes toggle chart lines
- [ ] Chart switches between Line/Bar mode
- [ ] Timeline shows recent entries
- [ ] CSV export downloads correctly
- [ ] Statistics cards show correct averages

---

## 🧮 Logic Testing

### Test 5: Score Interpretation Logic

**Test Cases:**
- [ ] Score ≥ 0.7 → Level = "高い (High)"
- [ ] Score 0.4-0.69 → Level = "中程度 (Medium)"
- [ ] Score < 0.4 → Level = "低い (Low)"

**Manual Test:**
1. Get a record with known scores
2. Verify interpretation matches expected level
3. Check that `high_description` is used for high scores
4. Check that `low_description` is used for low scores

---

### Test 6: Big Five Calculation

**Formula Test:**
- [ ] Extraversion = (a_score - b_score + 1) / 2
- [ ] Neuroticism = (c_score - d_score + 1) / 2
- [ ] Openness = (h_score - i_score + 1) / 2
- [ ] Agreeableness = (k_score - j_score + 1) / 2
- [ ] Conscientiousness = (l_score - m_score + 1) / 2

**Manual Calculation:**
```
Example: a_score=0.8, b_score=0.2
Expected Extraversion = (0.8 - 0.2 + 1) / 2 = 0.8
```

Verify in API response matches manual calculation.

---

## ⚠️ Error Handling

### Test 7: Error Scenarios

**Test invalid patient_id:**
```bash
curl "http://localhost:8000/api/japanese_fy_results_detailed.php?patient_id=99999" | jq '.'
```
- [ ] Returns error message
- [ ] HTTP status indicates error (4xx/5xx)

**Test missing patient_id:**
```bash
curl "http://localhost:8000/api/japanese_fy_results_detailed.php" | jq '.'
```
- [ ] Returns error: "Please provide patient_id or id parameter"

**Test missing autoload:**
- [ ] Check error handling when vendor/autoload.php is missing
- [ ] Verify helpful error message

---

## 🔒 Security Testing

### Test 8: Security Checks

- [ ] SQL injection test: `patient_id=2' OR '1'='1`
- [ ] XSS test: Patient text with `<script>alert('xss')</script>`
- [ ] CORS headers present and correct
- [ ] No sensitive data exposed in error messages
- [ ] Input validation working (negative IDs, strings, etc.)

---

## 📊 Performance Testing

### Test 9: Performance

- [ ] API responds in < 2 seconds with 50 records
- [ ] Page loads in < 3 seconds
- [ ] Charts render smoothly
- [ ] No memory leaks (check browser DevTools)

---

## ✅ Final Checklist

- [ ] All automated tests pass
- [ ] All manual tests pass
- [ ] No console errors
- [ ] No PHP warnings/notices
- [ ] Documentation is complete
- [ ] Code is committed to git
- [ ] Ready for production deployment

---

## 📝 Notes

Record any issues found:
