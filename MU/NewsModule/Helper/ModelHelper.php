<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Helper;

use MU\NewsModule\Helper\Base\AbstractModelHelper;

/**
 * Helper implementation class for model layer methods.
 */
class ModelHelper extends AbstractModelHelper
{
    /**
     * Returns a desired sorting criteria for passing it to a repository method.
     *
     * @param string $objectType Name of treated entity type
     * @param string $sorting    The type of sorting (newest, random, default)
     *
     * @return string The order by clause
     */
    public function resolveSortParameter($objectType = '', $sorting = 'default')
    {
        if ($sorting == 'random') {
            return 'RAND()';
        }
        
        $hasStandardFields = in_array($objectType, ['message']);
        
        $sortParam = '';
        if ($sorting == 'newest') {
            if (true === $hasStandardFields) {
                $sortParam = 'createdDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ($sorting == 'updated') {
            if (true === $hasStandardFields) {
                $sortParam = 'updatedDate DESC';
            } else {
                $sortParam = $this->entityFactory->getIdField($objectType) . ' DESC';
            }
        } elseif ($sorting == 'default') {
            $repository = $this->entityFactory->getRepository($objectType);
            $sortParam = $repository->getDefaultSortingField();
        } elseif ($sorting == 'startedlast') {
            $sortParam = 'startDate DESC';
        }
        
        return $sortParam;
    }
    // feel free to add your own convenience methods here
}
