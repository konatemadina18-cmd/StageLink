<?php

if (!function_exists('media_url')) {
    /**
     * Retourne l'URL correcte d'un fichier, qu'il soit stocké :
     * - sur Cloudinary (URL complète déjà en https://...)
     * - ou localement en ancien format (chemin relatif type "photos/xxx.jpg")
     *
     * Utile pendant la transition vers Cloudinary, pour ne pas casser
     * l'affichage des fichiers déjà existants avant la migration.
     */
    function media_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Si c'est déjà une URL complète (Cloudinary), on la retourne telle quelle.
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Sinon, comportement historique (fichier stocké localement).
        return asset('storage/' . $path);
    }
}