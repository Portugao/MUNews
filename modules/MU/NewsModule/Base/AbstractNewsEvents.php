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

namespace MU\NewsModule\Base;

use MU\NewsModule\Listener\EntityLifecycleListener;
use MU\NewsModule\Menu\MenuBuilder;

/**
 * Events definition base class.
 */
abstract class AbstractNewsEvents
{
    /**
     * The munewsmodule.itemactionsmenu_pre_configure event is thrown before the item actions
     * menu is built in the menu builder.
     *
     * The event listener receives an
     * MU\NewsModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    const MENU_ITEMACTIONS_PRE_CONFIGURE = 'munewsmodule.itemactionsmenu_pre_configure';
    
    /**
     * The munewsmodule.itemactionsmenu_post_configure event is thrown after the item actions
     * menu has been built in the menu builder.
     *
     * The event listener receives an
     * MU\NewsModule\Event\ConfigureItemActionsMenuEvent instance.
     *
     * @see MenuBuilder::createItemActionsMenu()
     * @var string
     */
    const MENU_ITEMACTIONS_POST_CONFIGURE = 'munewsmodule.itemactionsmenu_post_configure';
    
    /**
     * The munewsmodule.message_post_load event is thrown when messages
     * are loaded from the database.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    const MESSAGE_POST_LOAD = 'munewsmodule.message_post_load';
    
    /**
     * The munewsmodule.message_pre_persist event is thrown before a new message
     * is created in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    const MESSAGE_PRE_PERSIST = 'munewsmodule.message_pre_persist';
    
    /**
     * The munewsmodule.message_post_persist event is thrown after a new message
     * has been created in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    const MESSAGE_POST_PERSIST = 'munewsmodule.message_post_persist';
    
    /**
     * The munewsmodule.message_pre_remove event is thrown before an existing message
     * is removed from the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    const MESSAGE_PRE_REMOVE = 'munewsmodule.message_pre_remove';
    
    /**
     * The munewsmodule.message_post_remove event is thrown after an existing message
     * has been removed from the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    const MESSAGE_POST_REMOVE = 'munewsmodule.message_post_remove';
    
    /**
     * The munewsmodule.message_pre_update event is thrown before an existing message
     * is updated in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    const MESSAGE_PRE_UPDATE = 'munewsmodule.message_pre_update';
    
    /**
     * The munewsmodule.message_post_update event is thrown after an existing new message
     * has been updated in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterMessageEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    const MESSAGE_POST_UPDATE = 'munewsmodule.message_post_update';
    
    /**
     * The munewsmodule.image_post_load event is thrown when images
     * are loaded from the database.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::postLoad()
     * @var string
     */
    const IMAGE_POST_LOAD = 'munewsmodule.image_post_load';
    
    /**
     * The munewsmodule.image_pre_persist event is thrown before a new image
     * is created in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::prePersist()
     * @var string
     */
    const IMAGE_PRE_PERSIST = 'munewsmodule.image_pre_persist';
    
    /**
     * The munewsmodule.image_post_persist event is thrown after a new image
     * has been created in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::postPersist()
     * @var string
     */
    const IMAGE_POST_PERSIST = 'munewsmodule.image_post_persist';
    
    /**
     * The munewsmodule.image_pre_remove event is thrown before an existing image
     * is removed from the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::preRemove()
     * @var string
     */
    const IMAGE_PRE_REMOVE = 'munewsmodule.image_pre_remove';
    
    /**
     * The munewsmodule.image_post_remove event is thrown after an existing image
     * has been removed from the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::postRemove()
     * @var string
     */
    const IMAGE_POST_REMOVE = 'munewsmodule.image_post_remove';
    
    /**
     * The munewsmodule.image_pre_update event is thrown before an existing image
     * is updated in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::preUpdate()
     * @var string
     */
    const IMAGE_PRE_UPDATE = 'munewsmodule.image_pre_update';
    
    /**
     * The munewsmodule.image_post_update event is thrown after an existing new image
     * has been updated in the system.
     *
     * The event listener receives an
     * MU\NewsModule\Event\FilterImageEvent instance.
     *
     * @see EntityLifecycleListener::postUpdate()
     * @var string
     */
    const IMAGE_POST_UPDATE = 'munewsmodule.image_post_update';
}