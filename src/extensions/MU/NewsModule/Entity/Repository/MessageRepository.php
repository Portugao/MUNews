<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

declare(strict_types=1);

namespace MU\NewsModule\Entity\Repository;

use MU\NewsModule\Entity\Repository\Base\AbstractMessageRepository;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the concrete repository class for message entities.
 */
class MessageRepository extends AbstractMessageRepository
{
    /**
     * Retrieves an array with all fields which can be used for sorting instances.
     *
     * @return string[] Sorting fields array
     */
    public function getAllowedSortingFields(): array
    {
        return [
            'id',
            'workflowState',
            'title',
            'imageUpload1',
            'displayOnIndex',
            'createdBy',
            'createdDate',
            'updatedBy',
            'updatedDate',
            'startDate',
            'weight'
        ];
    }
}