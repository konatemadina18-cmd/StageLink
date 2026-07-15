<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryUploadService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        // Le SDK lit automatiquement la variable d'environnement CLOUDINARY_URL
        $this->cloudinary = new Cloudinary();
    }

    /**
     * Envoie un fichier vers Cloudinary et retourne son URL sécurisée (https).
     *
     * @param  UploadedFile  $file    Le fichier reçu du formulaire ($request->file('photo'))
     * @param  string        $folder  Le dossier de rangement sur Cloudinary (ex: 'photos', 'logos', 'cvs')
     * @return string|null            L'URL complète du fichier, ou null en cas d'échec
     */
    public function upload(UploadedFile $file, string $folder): ?string
    {
        // Les documents (PDF, DOCX...) doivent être envoyés en tant que "raw"
        // pour que Cloudinary ne tente pas de les traiter comme des images.
        $resourceType = in_array(strtolower($file->getClientOriginalExtension()), ['pdf', 'doc', 'docx'])
            ? 'raw'
            : 'image';

        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'        => $folder,
            'resource_type' => $resourceType,
        ]);

        return $result['secure_url'] ?? null;
    }

    /**
     * Supprime un fichier de Cloudinary à partir de son URL complète.
     */
    public function delete(?string $url, string $resourceType = 'image'): void
    {
        if (!$url) {
            return;
        }

        // On extrait le "public_id" (identifiant Cloudinary) depuis l'URL stockée en base.
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return;
        }

        // Retire l'extension et les segments techniques de l'URL Cloudinary pour ne garder que le public_id
        $publicId = preg_replace('#^.*/upload/(?:v\d+/)?#', '', $path);
        $publicId = preg_replace('/\.[^.]+$/', '', $publicId);

        if ($publicId) {
            $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => $resourceType]);
        }
    }
}