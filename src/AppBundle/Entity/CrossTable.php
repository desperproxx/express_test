<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * CrossTable
 *
 * @ORM\Table(name="cross_table")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CrossTableRepository")
 */
class CrossTable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="teg_id", type="integer", nullable=true)
     */
    private $tegId;

    /**
     * @var int
     *
     * @ORM\Column(name="picture_id", type="integer", nullable=true)
     */
    private $newsId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tegId
     *
     * @param integer $tegId
     *
     * @return CrossTable
     */
    public function setTegId($tegId)
    {
        $this->tegId = $tegId;

        return $this;
    }

    /**
     * Get tegId
     *
     * @return int
     */
    public function getTegId()
    {
        return $this->tegId;
    }

    /**
     * Set newsId
     *
     * @param integer $newsId
     *
     * @return CrossTable
     */
    public function setNewsId($newsId)
    {
        $this->newsId = $newsId;

        return $this;
    }

    /**
     * Get newsId
     *
     * @return int
     */
    public function getNewsId()
    {
        return $this->newsId;
    }
}

