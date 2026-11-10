<?php

namespace App\Console\Commands;

use App\Support\ProductSeoRevision;
use Illuminate\Console\Command;
use RuntimeException;

class ApplyProductSeoRevision extends Command
{
    protected $signature = 'products:apply-seo-revision {file : Reviewed JSON payload} {--apply : Write eligible revisions} {--backup= : New JSONL backup file required for writes} {--report= : JSON result path}';
    protected $description = 'Apply reviewed SEO content only to currently in-stock products, preserving originals and commercial data.';

    public function handle(): int
    {
        $payload = json_decode(file_get_contents($this->argument('file')), true, 512, JSON_THROW_ON_ERROR);
        if (($payload['schema_version'] ?? null) !== 1 || !is_array($payload['products'] ?? null)) throw new RuntimeException('Unsupported revision format.');
        $ids = array_column($payload['products'], 'id');
        if (count(array_unique($ids)) !== count($ids)) throw new RuntimeException('Duplicate product IDs.');
        $handle = null;
        if ($this->option('apply')) {
            if (!$this->option('backup')) throw new RuntimeException('Use --backup with a new path.');
            $handle = fopen($this->option('backup'), 'x');
            if (!$handle) throw new RuntimeException('Cannot create exclusive backup file.');
        }
        $results = [];
        try {
            foreach ($payload['products'] as $entry) {
                $results[] = ProductSeoRevision::run($entry, (bool) $this->option('apply'), $handle ? function (array $row) use ($handle) {
                    $line = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
                    if (fwrite($handle, $line) !== strlen($line) || !fflush($handle)) throw new RuntimeException('Backup write failed; product was not modified.');
                } : null);
            }
        } finally {
            if ($handle) fclose($handle);
            if ($this->option('report')) file_put_contents($this->option('report'), json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        }
        foreach (array_count_values(array_column($results, 'status')) as $status => $count) $this->line("{$status}: {$count}");
        return self::SUCCESS;
    }
}
