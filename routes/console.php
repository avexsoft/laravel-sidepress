<?php

use Avexsoft\Sidepress\Sidepress;

Artisan::command('sidepress:install', function () {
    if (!config('sidepress.enabled')) {
        return;
    }
    $sp = new Sidepress();
    try {
        $original = file_get_contents($sp->index_php);
        $lines = Str::of($original)->explode(PHP_EOL);

        # find the first blank line after detecting 'maintenance'
        {
            $code = collect($lines);
            $scanned = $code->takeUntil(function ($value, $key) {
                return Str::contains($value, 'maintenance.php');
            });

            $startLine = count($scanned) + 1;

            $scanned = $code->takeUntil(function ($value, $key) use ($startLine, $sp) {
                if ($key <= $startLine) {
                    return false;
                }
                if (Str::contains($value, 'WP_USE_THEMES')) {
                    throw new \Exception("<error>`{$sp->index_php}` already patched by Sidepress</error>");
                }
                return trim($value, "\r\n") == '';
            });
        }

        $addCode[] = "else" . PHP_EOL;
        $addCode[] = "{" . PHP_EOL;
        $addCode[] = "    if (isset(\$_REQUEST['sidep'])) {" . PHP_EOL;
        $addCode[] = "        define('WP_USE_THEMES', true);" . PHP_EOL;
        $addCode[] = "        /** Loads the WordPress Environment and Template */" . PHP_EOL;
        $addCode[] = "        require __DIR__ . '/wp-blog-header.php';" . PHP_EOL;
        $addCode[] = "        die();" . PHP_EOL;
        $addCode[] = "    }" . PHP_EOL;
        $addCode[] = "}" . PHP_EOL;

        $code->splice(count($scanned), 0, $addCode);

        file_put_contents($sp->index_laravel_original_php, $original);
        file_put_contents($sp->index_php, $code->implode(''));

        $this->info("'{$sp->index_php}' is now patched with Sidepress");
    } catch (\Exception $e) {
        $this->info($e->getMessage());
    }

    // echo posix_getuid();
    $phar = file_get_contents('https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar');
    file_put_contents('wp-cli.phar', $phar);

    $ss = '';
    $ss .= '#!/bin/sh' . PHP_EOL;
    $ss .= "cp {$sp->index_php} {$sp->index_laravel_php}" . PHP_EOL;

    $ss .= 'cd public' . PHP_EOL;
    $ss .= '../wp-cli.phar --allow-root core download' . PHP_EOL;
    $ss .= 'echo' . PHP_EOL;
    $ss .= 'echo \# Wordpress installation complete' . PHP_EOL;
    $ss .= 'echo - We recommend Wordpress to use a separate database from Laravel' . PHP_EOL;
    $ss .= 'echo - Answer all mandatory questions to setup Wordpress correctly' . PHP_EOL;
    $ss .= 'echo - If you made a mistake, run \`./sidepress-install.sh\` again' . PHP_EOL;
    $ss .= 'echo - If \`public/wp-config.php\` already exists, delete it first' . PHP_EOL;
    $ss .= 'echo' . PHP_EOL;
    $ss .= '../wp-cli.phar --allow-root core config --prompt' . PHP_EOL;
    $ss .= '../wp-cli.phar --allow-root core install --prompt' . PHP_EOL;
    $ss .= 'cd ..' . PHP_EOL;

    $ss .= "cp {$sp->index_laravel_php} {$sp->index_php} " . PHP_EOL;
    $ss .= "rm {$sp->index_laravel_php}" . PHP_EOL;

    file_put_contents('sidepress-install.sh', $ss);

    chmod('sidepress-install.sh', 0755);
    chmod('wp-cli.phar', 0755);

    //           0        1         2         3         4         5         6         7         8
    //           12345678901234567890123456789012345678901234567890123456789012345678901234567890
    $output[] = "";
    $output[] = "Almost there! Follow the steps below to complete your Sidepress installaion:";
    $output[] = "";
    $output[] = "1. Try loading this Laravel project now, if it works, go to #5, else go to #2";
    $output[] = "";
    $output[] = "2. Examine `public/index.php` and see what we modified wrongly";
    $output[] = "3. If bug suspected, open a discussion at https://github.com/avexsoft/sidepress";
    $output[] = "4. Run `php artisan sidepress:uninstall` to undo the changes and stop here";
    $output[] = "";
    $output[] = "5. Run `./sidepress-install.sh` to download and install Wordpress";
    $output[] = "6. Make sure `route/web.php` does not handle the root route (`/`)";
    $output[] = "7. Try loading this Laravel project again, you should see the Wordpress";
    $output[] = "8. You're free to remove `./sidepress-install.sh` now";

    foreach ($output as $line) {
        $this->info($line);
    }
})->purpose("Patch `public/index.php` and create Wordpress install/download scripts");

Artisan::command('sidepress:uninstall', function () {
    $sp = new Sidepress();

    if (file_exists($sp->index_laravel_original_php)) {
        $original = file_get_contents($sp->index_laravel_original_php);
        file_put_contents($sp->index_php, $original);
        unlink($sp->index_laravel_original_php);
        $this->info("`{$sp->index_php}` restored using `{$sp->index_laravel_original_php}`");
    } else {
        $this->info("`{$sp->index_laravel_original_php}` not found, won't touch `{$sp->index_php}`");
    }

    @unlink('sidepress-install.sh');
    @unlink('wp-cli.phar');

    //           0        1         2         3         4         5         6         7         8
    //           12345678901234567890123456789012345678901234567890123456789012345678901234567890
    $output[] = "";
    $output[] = "1. The existing Wordpress in `public` is left untouched to protect your data";
    $output[] = "2. It has to be removed manually";
    $output[] = "3. Send us PRs or feedback at https://github.com/avexsoft/sidepress";
    $output[] = "4. Thank you for using Sidepress!";
    $output[] = "";

    foreach ($output as $line) {
        $this->info($line);
    }
})->purpose("Remove Sidepress patch from `public/index.php` and delete the scripts");

Artisan::command('sidepress:reinstall', function () {
    $this->call('sidepress:uninstall');
    $this->call('sidepress:install');
})->purpose("Uninstall and install again, required if you make changes to SIDEPRESS_ENABLED");
