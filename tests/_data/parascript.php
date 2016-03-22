<?php

$message = $argv[1];
$iterations = $argv[2];
for ($i=0; $i < $iterations; ++$i) {
    print "$message\n";
    sleep(1);
}
