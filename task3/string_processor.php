<?php
declare(strict_types=1);

function format_description(string $text, int $max_length = 50): string
{
    $shortText = mb_substr($text, 0, $max_length);

    if (mb_strlen($shortText) < mb_strlen($text))
    {
        $shortText .= "...";
    }

    return $shortText;
}

function parse_payment_description(string $description): array
{
    $result = [
        'order_id' => null,
        'description' => '',
        'amount' => null,
        'currency' => ''
    ];

    $orderPattern = '/#(\d+)/';

    if (preg_match($orderPattern, $description, $orderMatches))
    {
        $result['order_id'] = (int)$orderMatches[1];
    }

    $amountPattern = '/сумму\s+([0-9]+(?:\.[0-9]{2})?)\s+([А-Я]{3})/ui';

    if (preg_match($amountPattern, $description, $amountMatches))
    {
        $result['amount'] = (float)$amountMatches[1];
        $result['currency'] = $amountMatches[2];
    }

    $descPattern = '/^[^:]+:\s*(.+?)(?:\s+на сумму|\s*$)/u';

    if (preg_match($descPattern, $description, $descMatches))
    {
        $result['description'] = trim($descMatches[1]);
    }

    return $result;
}

function slugify_payment_id(string $id): string
{
    $slug = mb_strtolower($id);

    $translit = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];

    $slug = strtr($slug, $translit);
    $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}

function highlight_keywords(string $text, array $keywords): string
{
    if (empty($keywords) || empty($text))
    {
        return $text;
    }

    $pattern = '/(' . implode('|', $keywords) . ')/ui';

    $highlightStr = preg_replace_callback($pattern, function($matches) {
        return '<mark>' . $matches[1] . '</mark>';
    }, $text);

    return $highlightStr;
}