<?php
$lines = file('database/seeders/DatabaseSeeder.php');
$start = array_slice($lines, 0, 96);
$block_a = array_slice($lines, 96, 110);
$block_b = array_slice($lines, 206, 52);
$rest = array_slice($lines, 258);
file_put_contents('database/seeders/DatabaseSeeder.php', implode('', array_merge($start, $block_b, ["\n"], $block_a, $rest)));
echo "Done.";
