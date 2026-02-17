<?php

$services = require 'services.php';
$output = "## لیست کامل سرویس‌های پشتیبانی شده (Supported Services)\n\n";
$output .= "<details>\n<summary>برای مشاهده لیست <b>" . count($services) . "</b> سرویس فعال اینجا کلیک کنید</summary>\n\n";
$output .= "| ردیف | نام سرویس (کلید) | آدرس وب‌سایت |\n";
$output .= "| :---: | :--- | :--- |\n";

$i = 1;
foreach ($services as $key => $data) {
    if (is_array($data) && isset($data[0])) {
        $url = $data[0];
        $parsed = parse_url($url);
        $domain = isset($parsed['host']) ? str_replace('www.', '', $parsed['host']) : 'Unknown';
        
        // Persian name fallback (extract from key if possible, though manual mapping is ideal)
        // For now, key is better than nothing.
        $output .= "| $i | `$key` | [$domain]($url) |\n";
        $i++;
    }
}

$output .= "\n</details>\n"; // End details block

file_put_contents('services_block.md', $output);
echo "Service block generated.";
?>
