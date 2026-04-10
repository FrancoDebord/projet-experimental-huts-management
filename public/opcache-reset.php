<?php
// Script temporaire — à supprimer après usage
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache vidé avec succès.";
} else {
    echo "OPcache non activé ou indisponible.";
}
