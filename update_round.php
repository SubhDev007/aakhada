<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$round = \App\Models\Round::active()->first();
if ($round) {
    $round->end_time = now()->addSeconds(60);
    $round->save();
    echo "Round {$round->round_serial} updated to end at {$round->end_time}\n";
} else {
    echo "No active round found.\n";
}
