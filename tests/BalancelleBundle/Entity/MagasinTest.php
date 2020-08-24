<?php

namespace Tests\Entity;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Magasin;
use PHPUnit\Framework\TestCase;

class MagasinTest extends TestCase
{
    private $magasin;

    public function setUp()
    {
        parent::setUp();
        $this->magasin = new Magasin();
    }

    public function testGetAdresse()
    {
        $this->magasin->setAdresse('test');
        $this->assertEquals('test', $this->magasin->getAdresse());
    }

    public function testSetAdresse()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setAdresse($this->createMock(Magasin::class))
        );
    }

    public function testGetActive()
    {
        $this->magasin->setActive(true);
        $this->assertTrue($this->magasin->isActive());
        $this->magasin->setActive(false);
        $this->assertFalse($this->magasin->isActive());
    }

    public function testSetActive()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setActive(true)
        );
    }

    public function testGetPaiement()
    {
        $this->magasin->setPaiement('test');
        $this->assertEquals('test', $this->magasin->getPaiement());
    }

    public function testSetPaiement()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setPaiement('test')
        );
    }

    public function testGetHoraire()
    {
        $this->magasin->setHoraire('test');
        $this->assertEquals('test', $this->magasin->getHoraire());
    }

    public function testSetHoraire()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setHoraire('test')
        );
    }

    public function testGetCourses()
    {
        $course = new Course();
        $this->magasin->addCourse($course);
        $this->assertContains($course, $this->magasin->getCourses());
    }

    public function testAddCourse()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->addCourse(new Course())
        );
    }

    public function testRemoveCourse()
    {
        $course = new Course();
        $this->magasin->addCourse($course);
        $this->assertContains($course, $this->magasin->getCourses());
        $this->magasin->removeCourse($course);
        $this->assertNotContains($course, $this->magasin->getCourses());
    }

    public function testGetNom()
    {
        $this->magasin->setNom('test');
        $this->assertEquals('test', $this->magasin->getNom());
    }

    public function testSetNom()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setNom('test')
        );
    }

    public function testGetCommentaire()
    {
        $this->magasin->setCommentaire('test');
        $this->assertEquals('test', $this->magasin->getCommentaire());
    }

    public function testSetCommentaire()
    {
        $this->assertInstanceOf(
            Magasin::class,
            $this->magasin->setCommentaire('test')
        );
    }
}
