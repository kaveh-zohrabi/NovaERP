<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\BaseService;

class ExportService extends BaseService
{
    public function exportCsv(array $data, string $filename): string
    {
        $path = storage_path("app/exports/{$filename}.csv");
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $handle = fopen($path, 'w');

        if (! empty($data)) {
            fputcsv($handle, array_keys($data[0]));

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }
        }

        fclose($handle);

        return $path;
    }

    public function exportPdf(array $data, string $filename): string
    {
        $path = storage_path("app/exports/{$filename}.html");
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $html = $this->generateHtml($data, $filename);
        file_put_contents($path, $html);

        return $path;
    }

    private function generateHtml(array $data, string $title): string
    {
        $headers = ! empty($data) ? array_keys($data[0]) : [];
        $rows = $data;

        $html = '<!DOCTYPE html><html><head><title>'.$title.'</title>';
        $html .= '<style>table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f5f5f5}</style>';
        $html .= '</head><body>';
        $html .= '<h1>'.$title.'</h1>';
        $html .= '<table><thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th>'.e(ucwords(str_replace('_', ' ', $header))).'</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>'.e((string) $value).'</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return $html;
    }
}
