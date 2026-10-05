<?php
$root=dirname(__DIR__);
foreach(['VERSION','database/schema.sql','public/index.php','Docs/QAVNS.md','Docs/19-versioning.md'] as $f){if(!is_file($root.'/'.$f))throw new RuntimeException("Missing $f");}
$v=trim(file_get_contents($root.'/VERSION'));if(!preg_match('/^\d{3}\.\d+\.\d+\.\d+(?:-(?:a|b|rc)[1-9])?(?:-.+)?$/',$v))throw new RuntimeException("Invalid QAVNS version: $v");
echo "Smoke checks passed: $v\n";
