<?php

namespace App\Utils;

class PersonalityTraits
{
    /**
     * Big Five Personality Traits mapped to Japanese FY scores
     * Based on the 10-item personality inventory
     */
    const TRAITS = [
        'a_score' => [
            'id' => 1,
            'name_en' => 'Extraversion (Positive)',
            'name_ja' => '外向性（積極的）',
            'description' => '外向的で、主体性を持っている',
            'high_description' => '社交的で活発、リーダーシップがある',
            'low_description' => 'ひかえめで、おとなしい',
            'dimension' => 'Extraversion',
            'direction' => 'positive'
        ],
        'b_score' => [
            'id' => 2,
            'name_en' => 'Extraversion (Negative)',
            'name_ja' => '外向性（消極的）',
            'description' => 'ひかえめで、おとなしい',
            'high_description' => '内向的で控えめ、慎重',
            'low_description' => '外向的で、主体性を持っている',
            'dimension' => 'Extraversion',
            'direction' => 'negative'
        ],
        'c_score' => [
            'id' => 3,
            'name_en' => 'Neuroticism (Positive)',
            'name_ja' => '神経症的傾向（高い）',
            'description' => '心配性で、うろたえやすい',
            'high_description' => '不安を感じやすく、ストレスに敏感',
            'low_description' => '落ち着いていて、気分が安定している',
            'dimension' => 'Neuroticism',
            'direction' => 'positive'
        ],
        'd_score' => [
            'id' => 4,
            'name_en' => 'Emotional Stability',
            'name_ja' => '情緒安定性',
            'description' => '落ち着いていて、気分が安定している',
            'high_description' => '冷静で感情的に安定している',
            'low_description' => '心配性で、うろたえやすい',
            'dimension' => 'Neuroticism',
            'direction' => 'negative'
        ],
        'h_score' => [
            'id' => 5,
            'name_en' => 'Openness (Positive)',
            'name_ja' => '開放性（高い）',
            'description' => '新しいことや、面白そうなことが好き',
            'high_description' => '創造的で好奇心が強い',
            'low_description' => '挑戦的ではない、平凡な人間',
            'dimension' => 'Openness',
            'direction' => 'positive'
        ],
        'i_score' => [
            'id' => 6,
            'name_en' => 'Openness (Negative)',
            'name_ja' => '開放性（低い）',
            'description' => '挑戦的ではない、平凡な人間',
            'high_description' => '伝統的で保守的',
            'low_description' => '新しいことや、面白そうなことが好き',
            'dimension' => 'Openness',
            'direction' => 'negative'
        ],
        'j_score' => [
            'id' => 7,
            'name_en' => 'Agreeableness (Negative)',
            'name_ja' => '協調性（低い）',
            'description' => '不満をもち、もめごとを起こしやすい',
            'high_description' => '批判的で競争的',
            'low_description' => '配慮をする、やさしい人間',
            'dimension' => 'Agreeableness',
            'direction' => 'negative'
        ],
        'k_score' => [
            'id' => 8,
            'name_en' => 'Agreeableness (Positive)',
            'name_ja' => '協調性（高い）',
            'description' => '配慮をする、やさしい人間',
            'high_description' => '思いやりがあり協力的',
            'low_description' => '不満をもち、もめごとを起こしやすい',
            'dimension' => 'Agreeableness',
            'direction' => 'positive'
        ],
        'l_score' => [
            'id' => 9,
            'name_en' => 'Conscientiousness (Positive)',
            'name_ja' => '誠実性（高い）',
            'description' => 'しっかりしていて、自分に厳しい',
            'high_description' => '責任感が強く勤勉',
            'low_description' => '自己管理が甘く、うっかりしている',
            'dimension' => 'Conscientiousness',
            'direction' => 'positive'
        ],
        'm_score' => [
            'id' => 10,
            'name_en' => 'Conscientiousness (Negative)',
            'name_ja' => '誠実性（低い）',
            'description' => '自己管理が甘く、うっかりしている',
            'high_description' => '柔軟だが計画性に欠ける',
            'low_description' => 'しっかりしていて、自分に厳しい',
            'dimension' => 'Conscientiousness',
            'direction' => 'negative'
        ]
    ];

    /**
     * Get trait information by score field name
     */
    public static function getTrait($scoreField)
    {
        return self::TRAITS[$scoreField] ?? null;
    }

    /**
     * Get all traits
     */
    public static function getAllTraits()
    {
        return self::TRAITS;
    }

    /**
     * Get trait description based on score value
     */
    public static function getScoreInterpretation($scoreField, $scoreValue)
    {
        $trait = self::getTrait($scoreField);
        if (!$trait) {
            return null;
        }

        $level = '';
        $interpretation = '';

        if ($scoreValue >= 0.7) {
            $level = '高い (High)';
            $interpretation = $trait['high_description'];
        } elseif ($scoreValue >= 0.4) {
            $level = '中程度 (Medium)';
            $interpretation = $trait['description'];
        } else {
            $level = '低い (Low)';
            $interpretation = $trait['low_description'];
        }

        return [
            'score_field' => $scoreField,
            'score_value' => $scoreValue,
            'trait_name_ja' => $trait['name_ja'],
            'trait_name_en' => $trait['name_en'],
            'level' => $level,
            'interpretation' => $interpretation,
            'dimension' => $trait['dimension']
        ];
    }

    /**
     * Get personality profile from all scores
     */
    public static function getPersonalityProfile($scores)
    {
        $profile = [];
        
        foreach (self::TRAITS as $scoreField => $trait) {
            if (isset($scores[$scoreField])) {
                $scoreValue = floatval($scores[$scoreField]);
                $profile[] = self::getScoreInterpretation($scoreField, $scoreValue);
            }
        }

        return $profile;
    }

    /**
     * Get Big Five dimensions summary
     */
    public static function getBigFiveSummary($scores)
    {
        $dimensions = [
            'Extraversion' => 0,
            'Neuroticism' => 0,
            'Openness' => 0,
            'Agreeableness' => 0,
            'Conscientiousness' => 0
        ];

        // Calculate average for each dimension
        // Extraversion: a_score (positive) vs b_score (negative)
        $dimensions['Extraversion'] = (floatval($scores['a_score'] ?? 0) - floatval($scores['b_score'] ?? 0) + 1) / 2;
        
        // Neuroticism: c_score (high) vs d_score (low)
        $dimensions['Neuroticism'] = (floatval($scores['c_score'] ?? 0) - floatval($scores['d_score'] ?? 0) + 1) / 2;
        
        // Openness: h_score (high) vs i_score (low)
        $dimensions['Openness'] = (floatval($scores['h_score'] ?? 0) - floatval($scores['i_score'] ?? 0) + 1) / 2;
        
        // Agreeableness: k_score (high) vs j_score (low)
        $dimensions['Agreeableness'] = (floatval($scores['k_score'] ?? 0) - floatval($scores['j_score'] ?? 0) + 1) / 2;
        
        // Conscientiousness: l_score (high) vs m_score (low)
        $dimensions['Conscientiousness'] = (floatval($scores['l_score'] ?? 0) - floatval($scores['m_score'] ?? 0) + 1) / 2;

        return $dimensions;
    }
}
