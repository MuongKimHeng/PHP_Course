<?php
declare(strict_types=1);

// JSON endpoint for pages/reports.php:  GET reports_data.php?from=YYYY-MM-DD&to=YYYY-MM-DD  (both optional)
require_once __DIR__ . '/classes.php';
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

/** Returns the validated Y-m-d string, null when empty, throws on garbage. */
function dateParam(string $key): ?string
{
    $v = $_GET[$key] ?? '';
    if (!is_string($v) || $v === '') {
        return null;
    }
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $v);
    if ($d === false || $d->format('Y-m-d') !== $v) {
        throw new InvalidArgumentException("Invalid date in “{$key}”.");
    }
    return $v;
}

try {
    if (!Tools::isAdmin()) {
        http_response_code(Tools::currentUser() === null ? 401 : 403);
        echo json_encode(['error' => 'Administrators only.'], JSON_THROW_ON_ERROR);
        exit;
    }

    $from = dateParam('from');
    $to = dateParam('to');
    if ($from !== null && $to !== null && $from > $to) {
        throw new InvalidArgumentException('“From” must not be after “to”.');
    }

    echo json_encode([
        'from'       => $from,
        'to'         => $to,
        'summary'    => Report::summary($from, $to),
        'byDay'      => Report::byDay($from, $to),
        'byItem'     => Report::byItem($from, $to),
        'byCustomer' => Report::byCustomer($from, $to),
    ], JSON_THROW_ON_ERROR);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error.'], JSON_THROW_ON_ERROR);
}
