<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache reset successful!";
} else {
    echo "OPcache is not enabled or opcache_reset() function is not available.";
}
?>