$(document).ready(function() {
  // $('body').on('submit', '.validate-form', function(e) {
  //   e.preventDefault();
  //   $();
  //   alert('bitte mind.  100 Zeichen Werbetext eingeben');
  //   return false;
  // });

  //$('.validate-form').validate();

  $('.list-ad-text').collapser({
    mode: 'lines',
    truncate: 5,
    showText: 'Mehr anzeigen',
    hideText: 'Zuklappen'
  });

  $('.btn-hide').click(function() {
    $(this)
      .closest('.card')
      .find('.hide-this')
      .toggle();
    if ($(this).text() == 'Hide Details') $(this).text('Show Details');
    else $(this).text('Hide Details');
  });

  $('.semester-slider').each(function() {
    $(this).slider({
      range: true,
      min: 1,
      max: 15,
      values: [$(this).attr('data-start'), $(this).attr('data-end')],
      slide: function(event, ui) {
        $(ui.handle).text(ui.value);
      },
      change: function(event, ui) {
        $(this)
          .prev()
          .val(ui.values[0] + ',' + ui.values[1]);
      }
    });
    $(this)
      .find('.ui-slider-handle')
      .first()
      .text($(this).attr('data-start'));
    $(this)
      .find('.ui-slider-handle')
      .last()
      .text($(this).attr('data-end'));
  });

  $('.permanent').change(function() {
    if ($(this).is(':checked')) {
      $('.visible').hide();
      $('#visible_from').val('');
      $('#visible_to').val('');
      $('#visible_from').prop('required', false);
      $('#visible_to').prop('required', false);
    } else {
      $('.visible').show();
      $('#visible_from').prop('required', true);
      $('#visible_to').prop('required', true);
    }
  });
  $('.permanent').trigger('change');

  $('.bachelor').change(function() {
    if ($(this).is(':checked')) {
      $('.bachelor-slider').show();
    } else {
      $('.bachelor-slider').hide();
    }
  });
  $('.bachelor').trigger('change');

  $('.diplom').change(function() {
    if ($(this).is(':checked')) {
      $('.diplom-slider').show();
    } else {
      $('.diplom-slider').hide();
    }
  });
  $('.diplom').trigger('change');

  $('.master').change(function() {
    if ($(this).is(':checked')) {
      $('.master-slider').show();
    } else {
      $('.master-slider').hide();
    }
  });
  $('.master').trigger('change');

  $('#check-all-faculties').change(function() {
    if ($(this).is(':checked')) {
      $('.check-faculty').prop('checked', true);
    } else {
      $('.check-faculty').prop('checked', false);
    }
  });

  $('.submit-form')
    .click(function() {
      if ($('.check-faculty:checked').length > 0) {
        $('.check-faculty').prop('required', false);
      } else {
        $('.check-faculty').prop('required', true);
      }
    })
    .click(function() {
      if ($('.check-category:checked').length > 0) {
        $('.check-category').prop('required', false);
      } else {
        $('.check-category').prop('required', true);
      }
    })
    .click(function() {
      var elem = $(".web-link");
      var count = elem.filter(function() {
        return !$(this).val();
      }).length;
      if (count == elem.length) {
        $('.web-link').prop('required', true);
      } else {
        $('.web-link').prop('required', false);
      }
    });

  $('.delete-offer').click(function(event) {
    event.preventDefault();
    $.ajax({
      url: $(this).attr('href')
    }).done(function(html) {
      var modalContent = $(html)
        .find('#delete-modal')
        .html();
      $('#popup')
        .html(modalContent)
        .modal();
    });
  });

  //close Modal
  $('body').on('click', '.close-popup', function() {
    $('#popup').modal('hide');
  });
});
