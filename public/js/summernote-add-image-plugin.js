(function(factory) {
  /* global define */
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['jquery'], factory);
  } else if (typeof module === 'object' && module.exports) {
    // Node/CommonJS
    module.exports = factory(require('jquery'));
  } else {
    // Browser globals
    factory(window.jQuery);
  }
}(function($) {
  // Extends plugins for adding sgimage.
  //  - plugin is external module for customizing.
  $.extend($.summernote.plugins, {
    /**
     * @param {Object} context - context object has status of editor.
     */
    'sgimage': function(context) {
      var self = this;

      // ui has renders to build ui elements.
      //  - you can create a button with `ui.button`
      var ui = $.summernote.ui;

      // add sgimage button
      context.memo('button.sgimage', function() {
        // create button
        var button = ui.button({
          contents: '<i class="fa fa-image"/>',
          tooltip: 'Agregar Imagen',
          click: function() {
            //self.$panel.show();
            //self.$panel.hide(500);
            // invoke insertText method with 'sgimage' on editor module.
            Selecter.setContext(context);
            self.$modal.modal('show');
            //context.invoke('editor.insertText', 'sgimage');
          }
        });

        // create jQuery object from button instance.
        var $sgimage = button.render();
        return $sgimage;
      });

      // This events will be attached when editor is initialized.
      this.events = {
        // This will be called after modules are initialized.
        'summernote.init': function(we, e) {
          console.log('summernote initialized', we, e);
        },
        // This will be called when user releases a key on editable.
        'summernote.keyup': function(we, e) {
          //console.log('summernote keyup', we, e);
        }
      };

      // This method will be called when editor is initialized by $('..').summernote();
      // You can create elements for plugin
      this.initialize = function() {

        console.log('inicializados');
        this.$modal = $('#new-file');


      };


      // This methods will be called when editor is destroyed by $('..').summernote('destroy');
      // You should remove elements on `initialize`.
      this.destroy = function() {
        /*this.$panel.remove();
        this.$panel = null;*/
      };
    }
  });
}));
