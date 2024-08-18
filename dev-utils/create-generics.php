<?php

define('UTILS_PATH', 'src/Utils/');
define('LISTS_PATH', UTILS_PATH . '/Lists/');

function createGenerics(): void
{
    $typePacks = loadTypePacks();
    deleteFiles();
    foreach ($typePacks as $pack) {
        extract($pack);
        createGenericSet($type, $name, $uses);
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

/** @return array{type: string, name: string, uses: string[]}[] */
function loadTypePacks(): array
{
    $content = file_get_contents(UTILS_PATH . 'T.php');
    preg_match_all('/^use.+?\\\\([A-Z][a-zA-Z0-9]*);/um', $content, $uses, PREG_SET_ORDER);
    preg_match_all('/public (?<type>[^\s]+)\s\$(?<name>[a-z\d]+)/ui', $content, $matches, PREG_SET_ORDER);
    /** @var array{type: string, name: string, uses: string[]}[] */
    $typePairs = [];
    foreach ($matches as $rawPair) {
        $typePairsItem = ['type' => $rawPair['type'], 'name' => ucfirst($rawPair['name']), 'uses' => []];
        $types = preg_split('/[|&]/u', $rawPair['type']) ?: [];
        foreach ($uses as $matchesOfUse) {
            if (in_array($matchesOfUse[1], $types)) {
                $typePairsItem['uses'][] = $matchesOfUse[0];
            }
        }
        $typePairs[] = $typePairsItem;
    }
    return $typePairs;
}

/**
 * @param string[] $uses
 */
function createGenericSet(string $type, string $name, array $uses): void
{
    $templates = array_filter(scandir(LISTS_PATH) ?: [], fn($file) => preg_match('/Template/u', $file));
    foreach ($templates as $template) {
        $content = file_get_contents(LISTS_PATH . $template);
        $processedContent = strval(preg_replace(
            [
                '/Template/u',
                '/(?<!\\\\)\bT\[\]/u',
                '/(?<!\\\\)\bT\b/u',
                '/^use .+?\\\\T;/um',
                '/\bint\|int\b/u',
                '/\b(int\|[a-zA-Z\d]+?)\|int\b/u',
                '/(\([^()]*\(int\)[^()]*\))\|\1/u',
                '/\n{3,}/u'
            ],
            [$name, "($type)[]", $type, join("\n", $uses), 'int', '\\1', '\\1', "\n\n"],
            $content,
        ));
        $newName = preg_replace('/Template/u', $name, $template);
        file_put_contents(LISTS_PATH . $newName, $processedContent);
    }
}
