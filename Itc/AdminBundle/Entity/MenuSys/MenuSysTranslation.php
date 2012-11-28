<?php

namespace Itc\AdminBundle\Entity\MenuSys;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translator\Entity\Translation;

/**
 * @ORM\Table(
 *         indexes={@ORM\Index(name="person_translations_lookup_idx", columns={
 *             "locale", "translatable_id"
 *         })},
 *         uniqueConstraints={@ORM\UniqueConstraint(name="person_lookup_unique_idx", columns={
 *             "locale", "translatable_id", "property"
 *         })}
 * )
 * @ORM\Entity
 */
class MenuSysTranslation extends Translation
{
    /**
     * @ORM\ManyToOne(targetEntity="MenuSys", inversedBy="translations", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $translatable;
}