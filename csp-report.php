<?php
// Collects browser CSP violation reports (see report-uri in web.config).
// Append-only JSON-lines log; no output. Remove once the enforced
// Content-Security-Policy has proven clean.
$log_dir = __DIR__ . '/logs';
$log_file = $log_dir . '/csp-violations.log';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$report = file_get_contents('php://input');
if ($report !== false && $report !== '' && strlen($report) < 20000) {
    $line = date('c') . ' ' . $report . "\n";
    file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
    // Keep the log from growing without bound (last ~200 KB).
    clearstatcache(true, $log_file);
    if (filesize($log_file) > 204800) {
        $tail = file_get_contents($log_file, false, null, -102400);
        file_put_contents($log_file, $tail, LOCK_EX);
    }
}
http_response_code(204);
