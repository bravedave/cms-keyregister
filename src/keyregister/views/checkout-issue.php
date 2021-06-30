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

use strings;
use theme;

$dto = $this->data->dto;  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <input type="hidden" name="action" value="key-checkout">
  <input type="hidden" name="id" value="<?= $dto->id ?>">
  <input type="hidden" name="people_id" value="<?= $dto->people_id ?>">

  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-row mb-2">
            <div class="col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">type</div>
                </div>

                <input type="text" class="form-control" value="<?= config::keyset_text($dto->keyset_type) ?>" readonly>

              </div>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col-md-3 pt-2 pb-0">property</div>
            <div class="col py-2"><?= $dto->address_street ?></div>

          </div>

          <div class="form-row mb-2">
            <div class="offset-md-3 col">
              <div class="input-group">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <i class="bi bi-phone"></i>

                  </div>

                </div>

                <input type="tel" class="form-control" name="mobile">

                <div class="input-group-append">
                  <button id="<?= $_mobileCheck = strings::rand() ?>" type="button" class="btn input-group-text">check</button>

                </div>

              </div>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="offset-md-3 col">
              <input type="text" class="form-control" name="name" readonly>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="offset-md-3 col">
              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="direction" value="issue" id="<?= $uid = strings::rand() ?>" checked>

                <label class="form-check-label" for="<?= $uid ?>">
                  Issue

                </label>

              </div>

              <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="direction" value="return" id="<?= $uid = strings::rand() ?>">

                <label class="form-check-label" for="<?= $uid ?>">
                  return

                </label>

              </div>

            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">cancel</button>
          <button type="submit" class="btn btn-primary" disabled>save</button>

        </div>

      </div>

    </div>

  </div>
  <script>
    (_ => $('#<?= $_modal ?>')
      .on('shown.bs.modal', () => {
        $('#<?= $_form ?>')
          .on('submit', function(e) {
            let _form = $(this);
            let _data = _form.serializeFormJSON();

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: _data,

            }).then(d => {
              if ('ack' == d.response) {
                $('#<?= $_modal ?>').trigger('issue-key', d);
                localStorage.setItem('keyregister-mobile', _data.mobile);

              } else {
                _.growl(d);

              }

              $('#<?= $_modal ?>').modal('hide');

            });

            return false;

          })
          .on('verify-mobile', function(e) {
            let _form = $(this);
            let _data = _form.serializeFormJSON();

            if (String(_data.mobile).IsMobilePhone()) {
              _.post({
                url: _.url('<?= $this->route ?>'),
                data: {
                  action: 'get-person-by-mobile',
                  mobile: _data.mobile

                },

              }).then(d => {
                if ('ack' == d.response) {
                  $('input[name="people_id"]', _form).val(d.data.id);
                  $('input[name="name"]', _form).val(d.data.name);
                  $('button[type="submit"]', _form).prop('disabled', false).focus();

                } else {
                  _.growl(d);

                }

              });

            }

          });

        $('#<?= $_mobileCheck ?>')
          .on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();

            $('#<?= $_form ?>').trigger('verify-mobile');

          });

        $('input[name="mobile"]', '#<?= $_form ?>')
          .on('keydown', function(e) {
            if (13 == e.keyCode) {
              $('#<?= $_form ?>').trigger('verify-mobile');

            }

          });

        let m = localStorage.getItem('keyregister-mobile');
        if (String(m).IsMobilePhone()) {
          $('#<?= $_form ?> input[name="mobile"]').val(m);

        }
        $('#<?= $_form ?> input[name="mobile"]').focus();

      })
    )(_brayworth_);

  </script>
</form>