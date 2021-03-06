import {
  colorEmptyRequiredInputs,
  createAttributeMapObject,
  createBootstrapModal
} from 'ExtraFormBundle/utils/utils.js';

import $ from 'jquery';

export default function loadStepEditors () {

  /**
   * Create the editor for each textareas with the class step-editor
   */
  $('textarea.step-editor').each(function (index) {

    var textarea = this;

    import(/* webpackChunkName: "bootstrap-vue-step-editor" */ './app').then(function (app) {
      var editorComponentId = 'extraStepEditorComponent' + index;

      // Do not load the editor if it was already loaded
      if (document.getElementById(editorComponentId)) {
        return;
      }

      // Retrieve the textarea attributes and value
      var formProperties = createAttributeMapObject(textarea);
      var configuration = window[formProperties['data-configuration-variable']];

      configuration.componentId = editorComponentId;
      var rawModal = createRawModal();
      var rawModalButton =
        '<button class="trigger-extra-step-raw-mode-modal-' + index + '">' +
          'Raw mode' +
        '</button>';
      var advancedModal = createAdvancedModal();
      var advancedModalButton =
        '<button class="trigger-extra-step-advanced-visual-mode-modal-' + index + '">' +
          'Visual mode' +
        '</button>';

      /**
       * Insert buttons in place of the textarea
       */
      $(textarea).after(
        '<div class="modal-buttons">' +
           advancedModalButton + ' ' + rawModalButton +
        '</div>'
      );

      // Insert the modals editor at the end of the body
      var $body = $('body');

      $body.append(
        '<div id="' + editorComponentId + '">' + rawModal + advancedModal + '</div>'
      );

      // Hide the initial textarea
      textarea.style.display = 'none';

      // Display / Hide the modals
      var modalTypes = [
        'extra-step-advanced-visual-mode-modal',
        'extra-step-raw-mode-modal'
      ];

      modalTypes.forEach(function (modalType) {
        showModalOnClick(modalType, index);
      });

      modalTypes.forEach(function (modalType) {
        hideModalOnClick(modalType);
      });

      app.triggerVueStepEditor('#' + editorComponentId, configuration, formProperties);

      colorEmptyRequiredInputs(editorComponentId, 'extra-form-inputs-required');

      /**
       * Show modal on click on trigger button
       *
       * @param modalType
       * @param modalIdentifier
       */
      function showModalOnClick (modalType, modalIdentifier) {
        $(document).on('click', 'button.trigger-' + modalType + '-' + modalIdentifier, function (event) {
          event.preventDefault();
          var $modal = $('#' + modalType + '-' + modalIdentifier);

          $modal.modal('show');
        });
      }

      /**
       * Hide modal on click on close button
       *
       * @param modalType
       */
      function hideModalOnClick (modalType) {
        var classes =
          // On the generate field button from the editor-raw
          '.' + modalType + ' .modal-body button.close-modal, ' +

            // On the upper right cross of the modal
          '.' + modalType + ' .modal-footer > button.close-modal, ' +

            // On the close button on the left bottom of the modal
          '.' + modalType + ' .modal-header > button.close';

        $(document).on('click', classes, function (event) {
          event.preventDefault();
          $(this)
            .closest('.modal')
            .modal('hide')
          ;
        });
      }

      /**
       * Create the raw modal
       *
       * @returns {string}
       */
      function createRawModal () {
        return createBootstrapModal(
          index,
          'extra-step-raw-mode-modal',
          'modal-fullscreen',
          'Editor in raw mode',
          '<div class="editor">' +
            '<step-editor-raw></step-editor-raw>' +
          '</div><br>'
        );
      }

      /**
       * Create the advanced modal
       *
       * @returns {string}
       */
      function createAdvancedModal () {
        return createBootstrapModal(
          index,
          'extra-step-advanced-visual-mode-modal',
          'modal-fullscreen',
          'Visual mode',
          '<div class="editor extra-step-editor">' +
            '<step-editor></step-editor>' +
          '</div>',
          '<em>All your changes are automatically saved</em>'
        );
      }

    });

  });

};
