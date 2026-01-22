<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

$hasher = new NativePasswordHasher();
$hash = '$2y$13$IBbqywp1NzL1ehwyy5NkU.jGQSC7iJZ4MVqs7P9Nwu2hWTLFjvS6C';
$password = 'aaaaaaaa';

if (password_verify($password, $hash)) {
    echo "MATCH: The password is correct.\n";
} else {
    echo "MISMATCH: The password is NOT correct.\n";
}
