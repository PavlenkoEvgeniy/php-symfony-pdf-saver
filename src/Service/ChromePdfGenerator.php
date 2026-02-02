<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Process\Process;

final class ChromePdfGenerator
{
    public function generateFromHtml(
        string $html,
        string $outputPath,
        string $chromeBin = 'google-chrome-stable',
        ?int $timeout = 60,
        bool $noSandbox = false,
    ): void {
        $tempFile = \tempnam(\sys_get_temp_dir(), 'pdf-html-');
        if (false === $tempFile) {
            throw new \RuntimeException('Unable to create temporary HTML file.');
        }

        $inputPath = $tempFile . '.html';
        if (!\rename($tempFile, $inputPath)) {
            throw new \RuntimeException('Unable to prepare temporary HTML file.');
        }

        $bytes = \file_put_contents($inputPath, $html);
        if (false === $bytes) {
            @\unlink($inputPath);
            throw new \RuntimeException('Unable to write temporary HTML file.');
        }

        try {
            $this->generate(
                inputPath: $inputPath,
                outputPath: $outputPath,
                chromeBin: $chromeBin,
                timeout: $timeout,
                noSandbox: $noSandbox,
            );
        } finally {
            @\unlink($inputPath);
        }
    }

    public function generate(
        string $inputPath,
        string $outputPath,
        string $chromeBin = 'google-chrome-stable',
        ?int $timeout = 60,
        bool $noSandbox = false,
    ): void {
        $args = [
            $chromeBin,
            '--headless',
            '--hide-scrollbars',
            '--disable-gpu',
            '--no-margins',
        ];

        if ($noSandbox) {
            $args[] = '--no-sandbox';
        }

        $args[] = '--print-to-pdf=' . $outputPath;
        $args[] = $inputPath;

        $process = new Process($args);
        $process->setTimeout(null !== $timeout && $timeout > 0 ? $timeout : null);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorOutput    = \trim($process->getErrorOutput());
            $standardOutput = \trim($process->getOutput());
            $details        = \trim($errorOutput . "\n" . $standardOutput);
            throw new \RuntimeException('' !== $details ? $details : 'Chrome process failed.');
        }
    }
}
