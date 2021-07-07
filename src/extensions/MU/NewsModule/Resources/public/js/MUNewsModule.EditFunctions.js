'use strict';

/**
 * Resets the value of an upload / file input field.
 */
function mUNewsResetUploadField(fieldName) {
    jQuery('#' + fieldName).attr('type', 'input');
    jQuery('#' + fieldName).attr('type', 'file');
}

/**
 * Initialises the reset button for a certain upload input.
 */
function mUNewsInitUploadField(fieldName) {
    jQuery('#' + fieldName + 'ResetVal').click(function (event) {
        event.preventDefault();
        mUNewsResetUploadField(fieldName);
    }).removeClass('d-none');
}

/**
 * Initialises the reset button for a certain date input.
 */
function mUNewsInitDateField(fieldName) {
    jQuery('#' + fieldName + 'ResetVal').click(function (event) {
        event.preventDefault();
        if ('DIV' == jQuery('#' + fieldName).prop('tagName')) {
            jQuery('#' + fieldName + '_date, #' + fieldName + '_time').val('');
        } else {
            jQuery('#' + fieldName + ', #' + fieldName + '').val('');
        }
    }).removeClass('d-none');
}

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function mUNewsTriggerFormValidation() {
    mUNewsExecuteCustomValidationConstraints(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function mUNewsHandleFormSubmit(event) {
    if (triggerValidation) {
        mUNewsTriggerFormValidation();
        if (!editForm.get(0).checkValidity()) {
            event.preventDefault();
            return false;
        }
    }

    // hide form buttons to prevent double submits by accident
    formButtons.each(function (index) {
        jQuery(this).addClass('d-none');
    });

    return true;
}

/**
 * Initialises an entity edit form.
 */
function mUNewsInitEditForm(mode, entityId) {
    if (jQuery('.munews-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.munews-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    if (jQuery('#moderationFieldsSection').length > 0) {
        jQuery('#moderationFieldsContent').addClass('d-none');
        jQuery('#moderationFieldsSection legend').css({cursor: 'pointer'}).click(function (event) {
            if (jQuery('#moderationFieldsContent').hasClass('d-none')) {
                jQuery('#moderationFieldsContent').removeClass('d-none');
                jQuery(this).find('i').removeClass('fa-expand').addClass('fa-compress');
            } else {
                jQuery('#moderationFieldsContent').addClass('d-none');
                jQuery(this).find('i').removeClass('fa-compress').addClass('fa-expand');
            }
        });
    }

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(function (event) {
        mUNewsExecuteCustomValidationConstraints(editedObjectType, editedEntityId);
    });

    formButtons = editForm.find('.form-buttons input, .form-buttons button');
    if (editForm.find('.btn-danger').length > 0) {
        editForm.find('.btn-danger').first().bind('click keypress', function (event) {
            if (!window.confirm(Translator.trans('Do you really want to delete this entry?'))) {
                event.preventDefault();
            }
        });
    }
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).attr('formnovalidate');
    });
    editForm.submit(mUNewsHandleFormSubmit);

    if ('create' !== mode) {
        mUNewsTriggerFormValidation();
    }
}

/**
 * Initialises a relation field section with optional edit capabilities.
 */
function mUNewsInitRelationHandling(objectType, alias, idPrefix, includeEditing, inputType, createUrl) {
    if (includeEditing) {
        mUNewsInitInlineEditingButtons(objectType, alias, idPrefix, inputType, createUrl);
    }
}

jQuery(document).ready(function () {
    if (jQuery('.relation-editing-definition').length > 0) {
        jQuery('.relation-editing-definition').each(function (index) {
            var editHandler = {
                alias: jQuery(this).data('alias'),
                prefix: jQuery(this).data('inline-prefix'),
                moduleName: jQuery(this).data('module-name'),
                objectType: jQuery(this).data('object-type'),
                inputType: jQuery(this).data('input-type'),
                windowInstanceId: null
            };
            mUNewsInlineEditHandlers.push(editHandler);
            mUNewsInitRelationHandling(
                jQuery(this).data('object-type'),
                jQuery(this).data('alias'),
                jQuery(this).data('prefix'),
                '1' == jQuery(this).data('include-editing'),
                jQuery(this).data('input-type'),
                jQuery(this).data('create-url')
            );
        });
    }
    if (jQuery('.field-editing-definition').length > 0) {
        jQuery('.field-editing-definition').each(function (index) {
            if ('user' === jQuery(this).data('field-type')) {
                initUserLiveSearch(jQuery(this).data('field-name'));
            } else if ('date' === jQuery(this).data('field-type')) {
                mUNewsInitDateField(jQuery(this).data('field-name'));
            } else if ('upload' === jQuery(this).data('field-type')) {
                mUNewsInitUploadField(jQuery(this).data('field-name'));
            }
        });
    }
    if (jQuery('#formEditingDefinition').length > 0) {
        mUNewsInitEditForm(jQuery('#formEditingDefinition').data('mode'), jQuery('#formEditingDefinition').data('entityid'));
    }
});
