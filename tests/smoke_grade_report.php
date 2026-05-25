<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$command = sprintf(
    'php %s app:grade-vulnerability-report %s --answer-key=%s --json',
    escapeshellarg($root.'/bin/console'),
    escapeshellarg($root.'/tests/fixtures/student-report.example.json'),
    escapeshellarg($root.'/config/grading/answer-key.example.json')
);

$output = [];
$exitCode = 0;
exec($command, $output, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, implode(PHP_EOL, $output).PHP_EOL);
    exit($exitCode);
}

$result = json_decode(implode(PHP_EOL, $output), true);
if (!is_array($result)) {
    fwrite(STDERR, 'La sortie de notation n’est pas un JSON valide.'.PHP_EOL);
    exit(1);
}

if (abs((float) ($result['score'] ?? -1) - 50.0) > 0.001) {
    fwrite(STDERR, sprintf('Score attendu: 50. Score obtenu: %s', json_encode($result['score'] ?? null)).PHP_EOL);
    exit(1);
}

if (($result['matched_cases'] ?? null) !== 1) {
    fwrite(STDERR, sprintf('Cas reconnus attendus: 1. Obtenu: %s', json_encode($result['matched_cases'] ?? null)).PHP_EOL);
    exit(1);
}

echo 'Smoke test OK'.PHP_EOL;
