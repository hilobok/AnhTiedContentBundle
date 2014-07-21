<?php

namespace Anh\TiedContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaperTie
 *
 * @ORM\Table(indexes={
 *      @ORM\Index(name="idx_parent", columns={ "parentId" }),
 *      @ORM\Index(name="idx_child", columns={ "childId" })
 * })
 * @ORM\Entity(repositoryClass="Anh\TiedContentBundle\Entity\PaperTieRepository")
 */
class PaperTie
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Anh\ContentBundle\Entity\Paper")
     * @ORM\JoinColumn(name="parentId", referencedColumnName="id")
     * TieAssert\SectionIsConfigured
     */
    protected $parent;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Anh\ContentBundle\Entity\Paper", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(name="childId", referencedColumnName="id", onDelete="CASCADE")
     * TieAssert\SectionIsConfigured
     */
    protected $child;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent
     *
     * @param  Paper $paper
     * @return Tie
     */
    public function setParent($paper)
    {
        $this->parent = $paper;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Paper
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set child
     *
     * @param  Paper $paper
     * @return Tie
     */
    public function setChild($paper)
    {
        $this->child = $paper;

        return $this;
    }

    /**
     * Get child
     *
     * @return Paper
     */
    public function getChild()
    {
        return $this->child;
    }

    public function getUrlParameters()
    {
        if ($this->getParent() === null) {
            return $this->getChild()->getUrlParameters();
        }

        $providers = array(
            'parent' => $this->getParent()->getUrlParameters(),
            'child' => $this->getChild()->getUrlParameters()
        );

        foreach ($providers as $key => $values) {
            foreach ($values as $name => $value) {
                $parameters[sprintf('%s_%s', $key, $name)] = $value;
            }
        }

        return $parameters;
    }
}