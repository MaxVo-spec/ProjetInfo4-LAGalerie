<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Fixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */

    public function load(ObjectManager $manager)
    {
        for($i = 1; $i < 30; $i++)
        {
            $article = new Article();
            $article->setTitle("Article N°".$i);
            $article->setImg("https://picsum.photos/400");
            $article->setText("Text N°".$i);
            $article->setNote(5);
            $article->setAuthor("Max");
            $manager->persist($article);

            for($j = 1; $j < rand(3, 4); $j++)
            {
              $commentaire = new Commentaire();
              $commentaire->setUser("CoolDude".$j);
              $commentaire->setMessage("Trop bien");
              $commentaire->setNote(rand(1,5));
              $commentaire->setArticle($article);
              $manager->persist($commentaire);
            }
        }

        $manager->flush();
    }

}