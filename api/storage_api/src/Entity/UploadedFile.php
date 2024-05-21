<?php

namespace App\Entity;

use Common\Traits\TimestampableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadedFileRepository")
 * @ORM\Table(name="storage_api__files")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class UploadedFile
{
    use TimestampableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public int $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    public string $fileName;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="uploaded_files", fileNameProperty="fileName", originalName="originalName")
     *
     * @var File|null
     */
    public File $file;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    public string $originalName;
}
