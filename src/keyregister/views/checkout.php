<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms\keyregister;

use strings;  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <div class="row mt-4">
    <div class="offset-md-4 col-md-4">
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">keyset</div>
        </div>

        <input type="number" class="form-control" name="keyset" autofocus required>

        <div class="input-group-prepend">
          <button class="btn input-group-text"><i class="bi bi-arrow-return-left"></i></button>

        </div>

      </div>

    </div>

  </div>

  <div class="row mt-4">
    <div class="offset-md-4 col-md-4" id="<?= $_log = strings::rand() ?>">

    </div>

  </div>

  <div class="row mt-4">
    <div class="offset-md-4 col-md-4" id="<?= $_your_keys = strings::rand() ?>">

    </div>

  </div>

  <script>
    (_ => {
      $('#<?= $_your_keys ?>')
        .on('update', function(e, id) {

          let _me = $(this);
          _me.html('');

          let m = localStorage.getItem('keyregister-mobile');
          if ((String(m).IsMobilePhone)) {
            (_ => {
              _.post({
                url: _.url('<?= $this->route ?>'),
                data: {
                  action: 'get-keys-for-person',
                  mobile: m

                },

              }).then(d => {
                if ('ack' == d.response) {
                  if (d.data.length > 0) {
                    _me.html('<h6>keys on issue ...</h6>');
                    (() => {
                      let row = $('<div class="form-row"></div>');
                      $('<div class="col-2 border-bottom small font-weight-bold">keyset</div>')
                        .appendTo(row);

                      $('<div class="col-6 border-bottom small font-weight-bold">address</div>')
                        .appendTo(row);

                      $('<div class="col-4 border-bottom small font-weight-bold text-center">date</div>')
                        .appendTo(row);

                      row.appendTo(_me);

                    })();
                    $.each(d.data, (i, ks) => {
                      // console.log(ks);
                      let row = $('<div class="form-row"></div>');
                      $('<div class="col-2"></div>')
                        .html(ks.keyset)
                        .appendTo(row);

                      $('<div class="col-6"></div>')
                        .html(ks.address_street)
                        .appendTo(row);

                      let m = _.dayjs(ks.issued);
                      $('<div class="col-4 text-center"></div>')
                        .html(m.format(_.dayjs().format('YYYYMMDD') == m.format('YYYYMMDD') ? 'hh:mm a' : 'l'))
                        .appendTo(row);

                      row.appendTo(_me);

                    });

                  } else {
                    _me.html('no keys on issue ...');

                  }

                }

              });

            })(_brayworth_);

          }

        })
        .trigger('update');

      $(document).ready(() => {
        $('#<?= $_form ?>')
          .on('submit', function(e) {
            let _form = $(this);
            let _data = _form.serializeFormJSON();

            _.get.modal(_.url('<?= $this->route ?>/checkout/?k=' + _data.keyset))
              .then(m => m.on('issue-key', (e, d) => {
                $('<div class="alert alert-success"></div>')
                  .html(d.description)
                  .appendTo('#<?= $_log ?>');

                $('#<?= $_your_keys ?>').trigger('update');

              }))
              .then(m => m.on('hidden.bs.modal', e => {
                $('input[name="keyset"]', _form).val('').focus();

              }));

            return false;

          });

        if (_.browser.isMobileDevice) {
          //~ $('#mobile').attr('inputmode','numeric').attr('pattern','[0-9]*');
          $('input[type="number"][name="keyset"]', '#<?= $_form ?>')
            .attr('inputmode', 'numeric')
            .attr('pattern', '[0-9]*');

        }

      })
    })(_brayworth_);
  </script>
</form>