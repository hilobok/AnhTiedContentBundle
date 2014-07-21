<?php

namespace Anh\TiedContentBundle\Entity;

use Anh\DoctrineResource\ORM\ResourceRepository;
use Anh\ContentBundle\Entity\Paper;
use Anh\TiedContentBundle\Entity\PaperTie;

class PaperTieRepository extends ResourceRepository
{
    public function findPrevTie(PaperTie $current)
    {
        return $this->fetchOne(
            array( // criteria
                    'parent' => $current->getParent(),
                    '%id' => [ '<' => $current ],
            ),
            array( // sorting
                'id' => 'desc',
            )
        );
    }

    public function findNextTie(PaperTie $current)
    {
        return $this->fetchOne(
            array( // criteria
                    'parent' => $current->getParent(),
                    '%id' => [ '>' => $current ],
            ),
            array( // sorting
                'id' => 'asc',
            )
        );
    }

    public function findTie(Paper $child = null)
    {
        $tie = ($child && $child->getId()) ? $this->fetchOne(array('child' => $child)) : null;

        // search also in not yet flushed entities too (needed for url generation for new ties)
        if (empty($tie)) {
            $entities = $this->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions();
            foreach ($entities as $entity) {
                if (($entity instanceof PaperTie) && $entity->getChild() == $child) {
                    return $entity;
                }
            }
        }

        return $tie;
    }
}
