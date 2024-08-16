<?php

define('UTILS_PATH', 'src/Utils/');
define('LISTS_PATH', UTILS_PATH . '/Lists/');

function createGenerics(): void
{
    $typePairs = loadTypePairs();
    deleteFiles();
    foreach ($typePairs as $pair) {
        extract($pair);
        createGenericSet($type, $name);
    }
}

function deleteFiles(): void
{
    foreach (scandir(LISTS_PATH) ?: [] as $file) {
        if (is_file(LISTS_PATH . $file) && !preg_match('/^(FindMode|IndexMode)|Template/u', $file)) {
            unlink(LISTS_PATH . $file);
        }
    }
}

/** @return array{type: string, name: string}[] */
function loadTypePairs(): array
{
    $content = file_get_contents(UTILS_PATH . 'T.php');
    preg_match_all('/public (?<type>[^\s]+)\s\$(?<name>[a-z\d]+)/ui', $content, $matches, PREG_SET_ORDER);
    /** @var array{type: string, name: string}[] */
    $typePairs = [];
    foreach ($matches as $rawPair) {
        $typePairs[] = ['type' => $rawPair['type'], 'name' => ucfirst($rawPair['name'])];
    }
    return $typePairs;
}

function createGenericSet(string $type, string $name): void
{
    $templates = array_filter(scandir(LISTS_PATH) ?: [], fn($file) => preg_match('/Template/u', $file));
    foreach ($templates as $template) {
        $content = file_get_contents(LISTS_PATH . $template);
        $processedContent = strval(preg_replace(
            ['/Template/u', '/(?<!\\\\)\bT\[\]/u', '/(?<!\\\\)\bT\b/u'],
            [$name, "($type)[]", $type],
            $content,
        ));
        $processedContent = strval(preg_replace(
            ['/\bint\|int\b/u', '/\b(int\|[a-zA-Z\d]+?)\|int\b/u', '/(\([^()]*\(int\)[^()]*\))\|\1/u'],
            ['int', '\\1', '\\1'],
            $processedContent,
        ));
        $newName = preg_replace('/Template/u', $name, $template);
        file_put_contents(LISTS_PATH . $newName, $processedContent);
    }
}
