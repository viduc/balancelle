<?php

namespace Tests\Repository;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Famille;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use BalancelleBundle\Repository\CourseRepository;

class CourseRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * ATTENTION!!! cette class de test permet de créer le code mais elle
     * utilise la base de données standard, donc à manier avec précaution!!
     */

    public function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
              ->get('doctrine')
              ->getManager();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testRecupererLesCoursesDuneFamille()
    {
        $famille = $this->entityManager
            ->getRepository(Famille::class)
            ->find(1);
        $course = $this->entityManager
            ->getRepository(Course::class)
            ->recupererLesCoursesDuneFamille($famille);
        $this->assertCount(1, $course);
    }
}
