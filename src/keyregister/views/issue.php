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
  <input type="hidden" name="action" value="key-issue">
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
            <div class="col-2 col-md-3 pt-2 text-truncate">keyset</div>
            <div class="col pt-2"><?= $dto->keyset ?></div>

            <div class="col-auto">
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
            <div class="col-md-3 col-form-label">person</div>
            <div class="col">
              <input type="text" class="form-control" name="name" required>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="offset-md-3 col">
              <input type="text" class="form-control" name="mobile" readonly>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="offset-md-3 col">
              <div>
                <img class="img-thumbnail w-100" id="<?= $_img = strings::rand() ?>" src="<?= strings::url($this->route . '/imageof/' . $dto->id . '?t=' . $dto->img_version) ?>">

              </div>

              <div id="<?= $_uidFileUpload = strings::rand() ?>"></div>

            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">cancel</button>
          <button type="submit" class="btn btn-primary">Issue Key</button>

        </div>

      </div>
    </div>
  </div>
  <script>
    (_ => $('#<?= $_modal ?>').on('shown.bs.modal', () => {
      $('#<?= $_form ?>')
        .on('submit', function(e) {
          let _form = $(this);
          let _data = _form.serializeFormJSON();

          _.post({
            url: _.url('<?= $this->route ?>'),
            data: _data,

          }).then(d => {
            if ('ack' == d.response) {
              $('#<?= $_modal ?>').trigger('success', d);

            } else {
              _.growl(d);

            }

            $('#<?= $_modal ?>').modal('hide');

          });

          return false;

        });

      $('#<?= $_form ?> input[name="name"]')
        .autofill({
          autoFocus: true,
          source: (request, response) => {
            _.post({
              url: _.url('people'),
              data: {
                action: 'search',
                term: request.term

              },

            }).then(d => {
              if ('ack' == d.response) {
                response(d.data);

              }

            });

          },
          select: (e, ui) => {
            let o = ui.item;
            if (o.id > 0) {
              $('#<?= $_form ?> input[name="people_id"]').val(o.id);
              $('#<?= $_form ?> input[name="mobile"]').val(String(o.mobile));

            }

          }

        });
      $('#<?= $_form ?> input[name="name"]').focus();

    }))(_brayworth_);
  </script>
</form>