(function ($, Drupal) {
    Drupal.behaviors.customReadMore = {
      attach: function (context, settings) {
        $('.read-more-toggle').click(function () {
          var $this = $(this);
          var $container = $this.closest('.custom-read-more-container');
          var $trimmedBody = $container.find('.trimmed-body');
          var $fullBody = $container.find('.full-body');
          
          $trimmedBody.toggle();
          $fullBody.toggle();
          
          if ($this.hasClass('show-less')) {
            $this.text('Read More');
          } else {
            $this.text('Show Less');
          }
          $this.toggleClass('show-less');
        });
      }
    };
  })(jQuery, Drupal);
  
  