<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('catégorie d\'un article');
        $manager->persist($category);

        for ($i = 1; $i < 20; $i++) {
            $article = new Article();
            $article->setTitle('Titre nuémro : '.$i)
            ->setDescription('Professionally disintermediate cooperative experiences without scalable ROI. Holisticly benchmark backward-compatible ROI vis-a-vis premium processes. Uniquely procrastinate diverse services for cooperative bandwidth. Progressively mesh stand-alone infrastructures before
             compelling models. Dramatically orchestrate multidisciplinary '.$i.'
             customer service after corporate content.Assertively parallel'.$i.' task standardized initiatives 
             rather than cross-media products. Conveniently engage empowered'.$i.' deliverables via bricks-and-clicks interfaces. 
             Professionally pursue focused applications whereas excellent'.$i.' e-commerce. Monotonectally aggregate effective initiatives whereas compelling'.$i)
            ->setImageName('https://i.picsum.photos/id/543/500/300.jpg')
            ->setPublished(true)
            ->setCreatedAt(new \DateTime())
            ->setCategory($category);
            $manager->persist($article);
        }

        $manager->flush();
    }
}
