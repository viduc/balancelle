<?php

namespace Tests\Entity;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Enfant;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class FamilleTest extends TestCase
{
    private $famille;

    public function setUp()
    {
        parent::setUp();
        $this->famille = new Famille();
    }

    public function testGetDateModification()
    {
        $this->famille->setDateModification(new \DateTime());
        $this->assertInstanceOf(
            \DateTime::class,
            $this->famille->getDateModification()
        );
    }

    public function testSetDateModification()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setDateModification(new \DateTime())
        );
    }

    public function testGetDateCreation()
    {
        $this->famille->setDateCreation(new \DateTime());
        $this->assertInstanceOf(
            \DateTime::class,
            $this->famille->getDateCreation()
        );
    }

    public function testSetDateCreation()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setDateCreation(new \DateTime())
        );
    }

    public function testGetNom()
    {
        $this->famille->setNom('test');
        $this->assertEquals('test', $this->famille->getNom());
    }

    public function testSetNom()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setNom('test')
        );
    }

    public function testGetParent1()
    {
        $parent = new User();
        $this->famille->setParent1($parent);
        $this->assertEquals($parent, $this->famille->getParent1());
    }

    public function testSetParent1()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setParent1(new User())
        );
    }

    public function testGetParent2()
    {
        $parent = new User();
        $this->famille->setParent2($parent);
        $this->assertEquals($parent, $this->famille->getParent2());
    }

    public function testSetParent2()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setParent2(new User())
        );
    }

    public function testGetNombrePermanence()
    {
        $this->famille->setNombrePermanence(1);
        $this->assertEquals(1, $this->famille->getNombrePermanence());
    }

    public function testSetNombrePermanence()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setNombrePermanence(1)
        );
    }


    public function testAddEnfant()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->addEnfant(new Enfant())
        );
    }

    public function testGetEnfants()
    {
        $enfant = new Enfant();
        $this->famille->addEnfant($enfant);
        $this->assertContains($enfant, $this->famille->getEnfants());
    }

    public function testRemoveEnfant()
    {
        $enfant = new Enfant();
        $this->famille->addEnfant($enfant);
        $this->assertContains($enfant, $this->famille->getEnfants());
        $this->famille->removeEnfant($enfant);
        $this->assertNotContains($enfant, $this->famille->getEnfants());
    }

    public function testGetCourses()
    {
        $course = new Course();
        $this->famille->addCourse($course);
        $this->assertContains($course, $this->famille->getCourses());
    }

    public function testAddCourse()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->addCourse(new Course())
        );
    }

    public function testRemoveCourse()
    {
        $course = new Course();
        $this->famille->addCourse($course);
        $this->assertContains($course, $this->famille->getCourses());
        $this->famille->removeCourse($course);
        $this->assertNotContains($course, $this->famille->getCourses());
    }

    public function testIsActive()
    {
        $this->famille->setActive(true);
        $this->assertTrue($this->famille->isActive());
        $this->famille->setActive(false);
        $this->assertFalse($this->famille->isActive());
    }

    public function testSetActive()
    {
        $this->assertInstanceOf(
            Famille::class,
            $this->famille->setActive(true)
        );
    }
}
