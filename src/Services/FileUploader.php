<?php

namespace App\Services;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    public function uploadFile(UploadedFile $file, $parameter): string
    {
        $fileName = md5(uniqid('', true)) . '.' . $file->guessClientExtension();
        $file->move(
            $parameter,
            $fileName
        );

        return $fileName;
    }
}