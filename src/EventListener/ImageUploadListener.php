<?php
// src/EventListener/ImageUploadListener.php

namespace App\EventListener;

use Vich\UploaderBundle\Event\Event;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
// use App\Entity\Events;

class ImageUploadListener
{
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function onImageUpload(Event $event)
    {
        $file = $event->getObject()->getImageFile();

        if ($file) {
            // Ouvrir l'image avec Imagine
            $image = $this->imagine->open($file->getRealPath());

            // Redimensionner l'image
            $image->resize(new Box(600, 800)); // Définir la largeur et la hauteur souhaitées
            // Sauvegarder l'image redimensionnée
            $image->save($file->getRealPath());
        }
    }
}
