'use strict';

/**
 * Helper function to create new modal form dialog instances.
 */
function mUNewsCreateInlineEditingWindowInstance(containerElem) {
    var newWindowId;

    // define the new window instance
    newWindowId = containerElem.attr('id') + 'Dialog';
    jQuery('<div />', { id: newWindowId })
        .append(
            jQuery('<iframe />', { src: containerElem.attr('href') })
                .css({ width: '100%', height: '440px' })
        )
        .dialog({
            autoOpen: false,
            show: {
                effect: 'blind',
                duration: 1000
            },
            hide: {
                effect: 'explode',
                duration: 1000
            },
            //title: containerElem.title,
            width: 600,
            height: 500,
            modal: false
        })
        .dialog('open');

    // return the identifier of dialog dom element
    return newWindowId;
}

/**
 * Observe a link for opening an inline window.
 */
function mUNewsInitInlineEditingWindow(objectType, idPrefix, containerId, inputType) {
    var found, newEditHandler;

    // whether the handler has been found
    found = false;

    // search for the handler
    jQuery.each(mUNewsInlineEditHandlers, function (key, editHandler) {
        // is this the right one
        if (editHandler.prefix !== containerId) {
            return;
        }

        // yes, it is
        found = true;
        // look whether there is already a window instance
        if (null !== editHandler.windowInstanceId) {
            // unset it
            jQuery('#' + editHandler.windowInstanceId).dialog('destroy');
        }
        // create and assign the new window instance
        editHandler.windowInstanceId = mUNewsCreateInlineEditingWindowInstance(jQuery('#' + containerId));
    });

    if (false !== found) {
        return;
    }

    // if no inline editing handler was found create a new one
    newEditHandler = {
        alias: idPrefix,
        prefix: containerId,
        moduleName: 'MUNewsModule',
        objectType: objectType,
        inputType: inputType,
        windowInstanceId: mUNewsCreateInlineEditingWindowInstance(jQuery('#' + containerId))
    };

    // add it to the list of edit handlers
    mUNewsInlineEditHandlers.push(newEditHandler);
}

/**
 * Creates a link for editing an existing item using inline editing.
 */
function mUNewsCreateInlineEditLink(objectType, idPrefix, elemPrefix, itemId) {
    var editHref, editLink;

    editHref = jQuery('#' + idPrefix + 'SelectorDoNew').attr('href') + '&id=' + itemId;
    editLink = jQuery('<a />', {
        id: elemPrefix + 'Edit',
        href: editHref,
        text: 'edit'
    }).append(
        jQuery('<span />', { class: 'fa fa-pencil-square-o' })
    );

    return editLink;
}

/**
 * Initialises behaviour for an inline editing link.
 */
function mUNewsInitInlineEditLink(objectType, idPrefix, elemPrefix, itemId, inputType) {
    jQuery('#' + elemPrefix + 'Edit').click(function (event) {
        event.preventDefault();
        mUNewsInitInlineEditingWindow(objectType, idPrefix, idPrefix + 'Reference_' + itemId + 'Edit');
    });
}

/**
 * Returns the input field reference for a given context
 */
function mUNewsDetermineInputReference(objectType, alias, idPrefix, inputType, targetWindow) {
    var inputPrefix, inputIdentifier, inputField;

    // determine reference to input element
    inputPrefix = targetWindow.jQuery('.munews-edit-form').first().attr('name');
    inputField = null;
    if (inputType === 'autocomplete') {
        inputIdentifier = idPrefix.replace('DoNew', '');
        inputField = targetWindow.jQuery('#' + inputIdentifier).first();
    } else if (inputType === 'select-single' || inputType === 'select-multi') {
        inputIdentifier = inputPrefix + '_' + alias;
        inputField = targetWindow.jQuery('#' + inputIdentifier).first();
    } else if (inputType === 'checkbox' || inputType === 'radio') {
        // points to the containing div element in this case
        inputIdentifier = inputPrefix + '_' + alias;
        inputField = targetWindow.jQuery('#' + alias + 'InlineEditingContainer').find('.form-group').first().find('div').first();
    }

    return {
        prefix: inputPrefix,
        identifier: inputIdentifier,
        field: inputField
    };
}

/**
 * Initialises inline editing capability for a certain form section.
 */
function mUNewsInitInlineEditingButtons(objectType, alias, idPrefix, inputType, createUrl) {
    var inputReference, createButtonId, createButton, itemIds, itemIdsArr;

    inputReference = mUNewsDetermineInputReference(objectType, alias, idPrefix, inputType, window);
    if (null === inputReference || null === inputReference.field) {
        return;
    }

    createButtonId = idPrefix + 'SelectorDoNew';

    if (jQuery('#' + createButtonId).length < 1) {
        if (inputType === 'autocomplete') {
            return;
        }
        // dynamically add create button
        createButton = jQuery('<a />', {
            id: createButtonId,
            href: createUrl,
            title: Translator.__('Create new entry'),
            class: 'btn btn-default munewsmodule-inline-button'
        }).append(
            jQuery('<i />', { class: 'fa fa-plus' })
        ).append(' ' + Translator.__('Create'));

        if (inputType === 'select-single' || inputType === 'select-multi') {
            inputReference.field.parent().append(createButton);
        } else if (inputType === 'checkbox' || inputType === 'radio') {
            inputReference.field.append(createButton);
        }
    }

    createButton = jQuery('#' + createButtonId);
    createButton.attr('href', createButton.attr('href') + '?raw=1&idp=' + createButtonId);
    createButton.click(function (event) {
        event.preventDefault();
        mUNewsInitInlineEditingWindow(objectType, idPrefix, createButtonId, inputType);
    });

    if (inputType === 'select-single' || inputType === 'select-multi') {
        // no edit buttons for select options
        return;
    }

    if (inputType === 'autocomplete') {
        itemIds = jQuery('#' + idPrefix).val();
        itemIdsArr = itemIds.split(',');
    } else if (inputType === 'checkbox' || inputType === 'radio') {
        itemIdsArr = [];
        inputReference.field.find('input').each(function (index) {
            var existingId, elemPrefix;

            existingId = jQuery(this).attr('value');
            itemIdsArr.push(existingId);

            elemPrefix = idPrefix + 'Reference_' + existingId + 'Edit';
            if (jQuery('#' + elemPrefix).length < 1) {
                jQuery(this).parent().append(' ').append(
                    jQuery('<a />', {
                        id: elemPrefix,
                        href: createUrl,
                        title: Translator.__('Edit this entry')
                    }).append(
                        jQuery('<span />', { class: 'fa fa-pencil-square-o' })
                    )
                );
            }
        });
    }
    jQuery.each(itemIdsArr, function (key, existingId) {
        var elemPrefix;

        if (existingId) {
            elemPrefix = idPrefix + 'Reference_' + existingId + 'Edit';
            if (jQuery('#' + elemPrefix) < 1) {
                return;
            }
            jQuery('#' + elemPrefix).attr('href', jQuery('#' + elemPrefix).attr('href') + '?raw=1&idp=' + elemPrefix);
            jQuery('#' + elemPrefix).click(function (event) {
                event.preventDefault();
                mUNewsInitInlineEditingWindow(objectType, idPrefix, elemPrefix, inputType);
            });
        }
    });
}

/**
 * Closes an iframe from the document displayed in it.
 */
function mUNewsCloseWindowFromInside(idPrefix, itemId, formattedTitle, searchTerm) {
    // if there is no parent window do nothing
    if (window.parent === '') {
        return;
    }

    // search for the handler of the current window
    jQuery.each(window.parent.mUNewsInlineEditHandlers, function (key, editHandler) {
        var inputType, inputReference, newElement, anchorElement;

        // look if this handler is the right one
        if (editHandler.prefix !== idPrefix) {
            return;
        }

        // determine reference to input element
        inputType = editHandler.inputType;
        inputReference = mUNewsDetermineInputReference(editHandler.objectType, editHandler.alias, idPrefix, inputType, window.parent);
        if (null === inputReference || null === inputReference.field) {
            return;
        }

        // show a message
        anchorElement = (inputType === 'autocomplete') ? inputReference.field : inputReference.field.parents('.form-group').first();
        window.parent.mUNewsSimpleAlert(anchorElement, window.parent.Translator.__('Information'), window.parent.Translator.__('Action has been completed.'), 'actionDoneAlert', 'success');

        // check if a new item has been created
        if (itemId > 0) {
            newElement = '';
            if (inputType === 'autocomplete') {
                // activate auto completion
                if (searchTerm == '') {
                    searchTerm = inputReference.field.val();
                }
                if (searchTerm != '') {
                    inputReference.field.autocomplete('option', 'autoFocus', true);
                    inputReference.field.autocomplete('search', searchTerm);
                    window.setTimeout(function () {
                        var suggestions = inputReference.field.autocomplete('widget')[0].children;
                        if (suggestions.length === 1) {
                            window.parent.jQuery(suggestions[0]).click();
                        }
                        inputReference.field.autocomplete('option', 'autoFocus', false);
                    }, 1000);
                }
            } else if (inputType === 'select-single' || inputType === 'select-multi') {
                newElement = jQuery('<option />', {
                    value: itemId,
                    selected: 'selected'
                }).text(formattedTitle);
            } else if (inputType === 'checkbox' || inputType === 'radio') {
                if (inputType === 'checkbox') {
                    newElement = jQuery('<label />', {
                        class: 'checkbox-inline'
                    }).append(
                        jQuery('<input />', {
                            type: 'checkbox',
                            id: inputReference.identifier + '_' + itemId,
                            name: inputReference.prefix + '[' + editHandler.alias + '][]',
                            value: itemId,
                            checked: 'checked'
                        })
                    ).append(' ' + formattedTitle);
                } else if (inputType === 'radio') {
                    newElement = jQuery('<label />', {
                        class: 'radio-inline'
                    }).append(
                        jQuery('<input />', {
                            type: 'radio',
                            id: inputReference.identifier + '_' + itemId,
                            name: inputReference.prefix + '[' + editHandler.alias + ']',
                            value: itemId,
                            checked: 'checked'
                        })
                    ).append(' ' + formattedTitle);
                }
            }
            inputReference.field.append(newElement);
        }

        // look whether there is a window instance
        if (null !== editHandler.windowInstanceId) {
            // close it
            window.parent.jQuery('#' + editHandler.windowInstanceId).dialog('close');
        }
    });
}
