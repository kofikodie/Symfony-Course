<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Pdf;
use App\Entity\Video;
use App\Entity\Author;

class InheritanceEntitiesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for($i=0; $i<=2; $i++){
            $author = new Author();
            $author->setName('Kofi' . $i);
            $manager->persist($author);

            for ($k=0; $k<=3; $k++ ){
                $pdf = new Pdf();
                $pdf->setFilename('PDF' . $i);
                $pdf->setDescription('description pdf' . $i);
                $pdf->setSize(1055);
                $pdf->setOrientation('portrait');
                $pdf->setPagesNumber(101);
                $pdf->setAuthor($author);
                $manager->persist($pdf);

            }

            for ($j = 0; $j <= 2; $j++){
                $video = new Video();
                $video->setFilename('Video' . $i);
                $video->setDescription('description video' . $i);
                $video->setSize(5055);
                $video->setDuration(360);
                $video->setFormat('mp4');
                $video->setAuthor($author);
                $manager->persist($video);
            }
        }

        $manager->flush();
    }
}
