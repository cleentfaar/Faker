<?php

namespace Faker\Provider\nl_NL;

class Lorem extends \Faker\Provider\Lorem
{
    /**
     * @var array
     */
    protected static $wordList = [];

    /**
     * @var array
     */
    protected static $sentenceList = [];

    /**
     * {@inheritdoc}
     */
    public static function word()
    {
        if (empty(self::$wordList)) {
            $wordList = array_map(function ($word) {
                $word = mb_strtolower($word);
                $word = trim($word, '.,?!');

                return $word;
            }, explode(' ', Text::getBaseText()));

            self::$wordList = $wordList;
        }

        return static::randomElement(self::$wordList);
    }

    /**
     * {@inheritdoc}
     */
    public static function sentence($nbWords = 6, $variableNbWords = true)
    {
        if ($nbWords <= 0) {
            return '';
        }

        if (empty(self::$sentenceList)) {
            preg_match_all('~.*?[?.!]~s', Text::getBaseText(), $sentences);
            $sentenceList = array_map(function ($sentence) {
                $sentence = trim(str_replace(PHP_EOL, ' ', $sentence));
                $sentence = str_replace(['’', '‘'], '', $sentence);

                return $sentence;
            }, $sentences[0]);

            self::$sentenceList = $sentenceList;
        }

        if ($variableNbWords) {
            $nbWords = self::randomizeNbElements($nbWords);
        }

        $useableSentences = [];

        foreach (self::$sentenceList as $sentence) {
            $wordCount = count(explode(' ', $sentence));
            if ($wordCount >= $nbWords) {
                $useableSentences[$wordCount][] = $sentence;
            }
        }

        ksort($useableSentences);
        $useableSentences = reset($useableSentences);
        $sentenceToUse = $useableSentences[array_rand($useableSentences, 1)];
        $wordsAdded = 0;
        $sentence = '';

        foreach (explode(' ', $sentenceToUse) as $word) {
            if ($wordsAdded >= $nbWords) {
                $sentence = rtrim($sentence, ',');
                $sentence .= '.';
                break;
            }

            $sentence .= ' '.$word;
            $wordsAdded++;
        }

        return trim($sentence);
    }
}
