<?php

namespace Zamion101\WideEvents;

use Illuminate\Support\Facades\File;

class GitHash
{
    /**
     * Retrieve the current commit hash.
     */
    public static function get(): ?string
    {
        // 1. Check if manually provided via ENV (Standard for PAAS like Heroku/Railroad)
        if ($hash = env('COMMIT_HASH') ?? env('GIT_SHA')) {
            return $hash;
        }

        // 2. Check for a REVISION file (Standard for Capistrano/Docker builds)
        $revisionFile = base_path('REVISION');
        if (File::exists($revisionFile)) {
            return trim(File::get($revisionFile));
        }

        // 3. Try to read .git/HEAD directly (Fastest for Local Dev - avoids 'exec' overhead)
        $gitPath = base_path('.git');
        if (File::isDirectory($gitPath)) {
            $head = trim(File::get($gitPath.'/HEAD'));

            // If HEAD points to a ref (e.g., ref: refs/heads/main)
            if (preg_match('/^ref: (.+)$/', $head, $matches)) {
                $refPath = $gitPath.'/'.$matches[1];
                if (File::exists($refPath)) {
                    return trim(File::get($refPath));
                }
            }

            // Detached HEAD or direct hash
            return $head;
        }

        // 4. Fallback to exec (Slowest, but works if .git exists but logic above fails)
        try {
            $hash = exec('git rev-parse --short HEAD');

            return $hash ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
