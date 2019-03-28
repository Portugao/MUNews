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

namespace MU\NewsModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use MU\NewsModule\Controller\Base\AbstractExternalController;

/**
 * Controller for external calls implementation class.
 *
 * @Route("/external")
 */
class ExternalController extends AbstractExternalController
{
    /**
     * @inheritDoc
     * @Route("/display/{objectType}/{id}/{source}/{displayMode}",
     *        requirements = {"id" = "\d+", "source" = "block|contentType|scribite", "displayMode" = "link|embed"},
     *        defaults = {"source" = "contentType", "displayMode" = "embed"},
     *        methods = {"GET"}
     * )
     */
    public function displayAction(
        Request $request,
        $objectType,
        $id,
        $source,
        $displayMode
    )
     {
        return parent::displayAction($request, $objectType, $id, $source, $displayMode);
    }

    /**
     * @inheritDoc
     * @Route("/finder/{objectType}/{editor}/{sort}/{sortdir}/{pos}/{num}",
     *        requirements = {"editor" = "ckeditor|quill|summernote|tinymce", "sortdir" = "asc|desc", "pos" = "\d+", "num" = "\d+"},
     *        defaults = {"sort" = "dummy", "sortdir" = "asc", "pos" = 1, "num" = 0},
     *        methods = {"GET"},
     *        options={"expose"=true}
     * )
     */
    public function finderAction(
        Request $request,
        $objectType,
        $editor,
        $sort,
        $sortdir,
        $pos = 1,
        $num = 0
    )
     {
        return parent::finderAction($request, $objectType, $editor, $sort, $sortdir, $pos, $num);
    }

    // feel free to extend the external controller here
}
