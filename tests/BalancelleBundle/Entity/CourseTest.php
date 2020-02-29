<?php

namespace Tests\Entity;

use BalancelleBundle\Entity\Course;
use BalancelleBundle\Entity\Famille;
use BalancelleBundle\Entity\Magasin;
use PHPUnit\Framework\TestCase;


class CourseTest extends TestCase
{
    private $course;

    public function setUp()
    {
        parent::setUp();
        $this->course = new Course();
    }

    public function testGetMagasin()
    {
        $this->course->setMagasin( $this->createMock(Magasin::class));
        $this->assertInstanceOf(Magasin::class, $this->course->getMagasin());
    }

    public function testSetMagasin()
    {
        $this->assertInstanceOf(
            Course::class,
            $this->course->setMagasin($this->createMock(Magasin::class))
        );
    }

    public function testGetFamille()
    {
        $this->course->setFamille( $this->createMock(Famille::class));
        $this->assertInstanceOf(Famille::class, $this->course->getFamille());
    }

    public function testSetFamille()
    {
        $this->assertInstanceOf(
            Course::class,
            $this->course->setFamille($this->createMock(Famille::class))
        );
    }

    public function testGetDate()
    {
        $this->course->setDateDebut(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->course->getDateDebut());
        $this->course->setDateFin(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $this->course->getDateFin());
    }

    public function testSetDate()
    {
        $this->assertInstanceOf(
            Course::class,
            $this->course->setDateDebut(new \DateTime())
        );
        $this->assertInstanceOf(
            Course::class,
            $this->course->setDateFin(new \DateTime())
        );
    }

    public function testGetCommentaire()
    {
        $this->course->setCommentaire('test');
        $this->assertEquals('test', $this->course->getCommentaire());
    }

    public function testSetCommentaire()
    {
        $this->assertInstanceOf(
            Course::class,
            $this->course->setCommentaire('test')
        );
    }

    public function testIsActive()
    {
        $this->course->setActive(true);
        $this->assertTrue($this->course->isActive());
        $this->course->setActive(false);
        $this->assertFalse($this->course->isActive());
    }

    public function testSetActive()
    {
        $this->assertInstanceOf(
            Course::class,
            $this->course->setActive(true)
        );
    }
}
