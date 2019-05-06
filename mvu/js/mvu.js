(function ($) {

  //$(document).ready(function() // one call but showing more data..
  // Please, read more about window.onload & $(document).ready(function()....

  window.onload = function(){
    $('details .crop-title').off('click').on('click', function() {
      var loading = false;
      var cropValue = $(this).attr('crop-value');
      var $this = $(this);
      if(loading == false) {
        $this.find('img').show();
        loading = true;
        $.ajax({
          'method' : 'POST',
          'url' : '/data/mvu/load',
          'data' : { cropValue: cropValue },
        }).done(function(result){
          console.log(result);
          $this.parent().find('.crop-detail').remove();
          $this.after(result.data);
          loading = false;
          $this.find('img').hide();
        });
        $this.addClass('open');
      }
    });
  }

})(jQuery);