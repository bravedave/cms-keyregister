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

  <input type="hidden" name="action" value="key-save">
  <input type="hidden" name="id" value="<?= $dto->id ?>">
  <input type="hidden" name="properties_id" value="<?= $dto->properties_id ?>">

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
            <div class="col-2 col-md-3 col-form-label text-truncate">keyset</div>
            <div class="col">
              <input type="text" class="form-control" name="keyset" maxlength="45" value="<?= $dto->keyset ?>" required>

            </div>

          </div>

          <?php if ($dto->id > 0) {  ?>
            <div class="form-row mb-2">
              <div class="col-2 col-md-3 col-form-label text-truncate">type</div>
              <div class="col">
                <select class="form-control" name="keyset_type" required>
                  <option value="">select type</option>
                  <?php
                  printf(
                    '<option value="%s" %s>%s</option>',
                    config::keyset_management,
                    config::keyset_management == $dto->keyset_type ? 'selected' : '',
                    config::keyset_management_label

                  );

                  printf(
                    '<option value="%s" %s>%s</option>',
                    config::keyset_tenant,
                    config::keyset_tenant == $dto->keyset_type ? 'selected' : '',
                    config::keyset_tenant_label

                  );

                  ?>

                </select>

              </div>

            </div>
          <?php }  ?>

          <div class="form-row mb-2">
            <div class="col-2 col-md-3 col-form-label text-truncate">location</div>
            <div class="col">
              <input type="text" class="form-control" name="location" maxlength="100" placeholder="key cabinet" id="<?= $_uid = strings::rand() ?>" value="<?= $dto->location ?>">

              <div class="mt-1 d-none" id="<?= $_uid ?>quick">
                <a class="btn btn-light btn-sm" href="#" id="<?= $_uid ?>tenant">tenant</a>
                <a class="btn btn-light btn-sm" href="#" id="<?= $_uid ?>clear">clear</a>

              </div>

              <script>
                (_ => $('#<?= $_modal ?>').on('shown.bs.modal', () => {

                  $('#<?= $_form ?> select[name="keyset_type"]')
                    .on('reconcile', function(e) {
                      let _form = $(this);
                      let _data = _form.serializeFormJSON();

                      if (<?= config::keyset_tenant ?> == Number(_data.keyset_type)) {
                        $('#<?= $_uid ?>quick').removeClass('d-none');

                      } else {
                        $('#<?= $_uid ?>quick').addClass('d-none');

                      }

                    })
                    .on('change', function(e) {
                      $(this).trigger('reconcile')
                    })
                    .trigger('reconcile');

                  $('#<?= $_uid ?>tenant')
                    .on('click', function(e) {
                      e.stopPropagation();
                      e.preventDefault();

                      $('#<?= $_uid ?>').val('tenant').focus().select();

                    });

                  $('#<?= $_uid ?>clear')
                    .on('click', function(e) {
                      e.stopPropagation();
                      e.preventDefault();

                      $('#<?= $_uid ?>').val('').focus();

                    });

                }))(_brayworth_);
              </script>

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col-md-3 col-form-label pb-0">property</div>
            <div class="col">
              <input type="text" class="form-control" name="address_street" value="<?= $dto->address_street ?>">

            </div>

          </div>

          <div class="form-row mb-2">
            <div class="col-md-3 col-form-label pb-0">description</div>
            <div class="col">
              <textarea class="form-control" name="description"><?= $dto->description ?></textarea>

            </div>

          </div>

          <?php if ($dto->id > 0) {  ?>
            <div class="form-row mb-2">
              <div class="col-md-3 d-none d-md-block">
                <?php if (config::enable_qr_codes) { ?>
                  <a href="<?= strings::url($this->route . '/qrcode/' . $dto->id . '/v') ?>" target="_blank">
                    <img class="img-thumbnail w-100" src="<?= strings::url(sprintf('%s/qrcode/%s', $this->route, $dto->id)) ?>">

                  </a>

                <?php } ?>

              </div>

              <div class="col">
                <div>
                  <img class="img-thumbnail w-100" data-ispdf="<?= $dto->haspdf ?>" id="<?= $_img = strings::rand() ?>" src="<?= strings::url($this->route . '/imageof/' . $dto->id . '?t=' . $dto->img_version) ?>">

                </div>

                <div id="<?= $_uidFileUpload = strings::rand() ?>"></div>

              </div>

            </div>

          <?php } ?>

        </div>

        <div class="modal-footer">
          <?php if ($dto->id > 0) {  ?>
            <button type="button" class="btn btn-outline-secondary mr-auto" id="<?= $_btnIssue = strings::rand() ?>">issue</button>
          <?php }  ?>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>
          <button type="submit" class="btn btn-primary">Save</button>

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
                $('#<?= $_modal ?>').trigger('success', d);

              } else {
                _.growl(d);

              }

              $('#<?= $_modal ?>').modal('hide');

            });

            return false;

          });

        $('#<?= $_form ?> textarea[name="description"]').autoResize();
        <?php if ($dto->id == 0) {  ?>
          /**
           * it's new
           * */
          $('#<?= $_form ?> input[name="keyset"]')
            .on('change', function(e) {
              let _me = $(this);
              if ('' != _me.val()) {
                _.post({
                  url: _.url('<?= $this->route ?>'),
                  data: {
                    action: 'get-keys',
                    archived : 0,
                    keyset: _me.val()

                  },

                }).then(d => {
                  _me.siblings('.alert').remove();
                  if ('ack' == d.response) {
                    if (!!d.data) {
                      $('#<?= $_form ?> button[type="submit"]').prop('disabled', true);
                      $('<div class="alert alert-danger mt-1"></div>')
                        .html(d.data.address_street)
                        .appendTo(_me.parent());
                    } else {
                      $('#<?= $_form ?> button[type="submit"]').prop('disabled', false);
                      _.growl(d);
                    }

                  } else {
                    $('#<?= $_form ?> button[type="submit"]').prop('disabled', false);
                    _.growl(d);

                  }

                });

              }

            });

        <?php }  ?>

        $('#<?= $_form ?> input[name="keyset"]').focus();
        $('#<?= $_form ?> input[name="address_street"]')
          .autofill({
            autoFocus: true,
            source: (request, response) => {
              _.post({
                url: _.url('properties'),
                data: {
                  action: 'search-properties',
                  term: request.term

                },

              }).then(d => response('ack' == d.response ? d.data : []));

            },
            select: (e, ui) => {
              let o = ui.item;
              if (o.id > 0) {
                $('#<?= $_form ?> input[name="properties_id"]').val(o.id);

              }

            }

          });

        <?php if ($dto->id > 0) {  ?>
          let rotate = (id, cmd) => {
            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: cmd,
                id: id

              },

            }).then(d => {
              if ('ack' == d.response) {
                $('#<?= $_img ?>').trigger('refresh');

              } else {
                _.growl(d);

              }

            });

          };

          $('#<?= $_form ?>')
            .on('delete-image', function(e) {
              let _form = $(this);
              let _data = _form.serializeFormJSON();

              _.post({
                url: _.url('<?= $this->route ?>'),
                data: {
                  action: 'key-remove-image',
                  id: _data.id

                },

              }).then(d => {
                if ('ack' == d.response) {
                  $('#<?= $_img ?>').trigger('refresh');

                } else {
                  _.growl(d);

                }

              });

            })
            .on('rotate-image-left', function(e) {
              let _form = $(this);
              let _data = _form.serializeFormJSON();

              rotate(_data.id, 'rotate-image-left');

            })
            .on('rotate-image-right', function(e) {
              let _form = $(this);
              let _data = _form.serializeFormJSON();

              rotate(_data.id, 'rotate-image-right');

            })
            .on('view-pdf', function(e) {
              let _form = $(this);
              let _data = _form.serializeFormJSON();

              window.open(_.url('<?= $this->route ?>/pdfof/' + _data.id));

            });

          $('#<?= $_img ?>')
            .on(_.browser.isMobileDevice ? 'click' : 'contextmenu', function(e) {
              if (e.shiftKey)
                return;

              e.stopPropagation();
              e.preventDefault();
              _.hideContexts();

              let _me = $(this);
              let _data = _me.data();
              let _context = _.context();

              if ('yes' == _data.ispdf) {
                _context.append($('<a href="#"><i class="bi bi-box-arrow-up-right"></i>view</a>').on('click', function(e) {
                  e.stopPropagation();
                  e.preventDefault();
                  _context.close();

                  $('#<?= $_form ?>').trigger('view-pdf')

                }));

              } else {
                _context.append($('<a href="#"><i class="bi bi-arrow-counterclockwise"></i>rotate left</a>').on('click', function(e) {
                  e.stopPropagation();
                  e.preventDefault();
                  _context.close();

                  $('#<?= $_form ?>').trigger('rotate-image-left')

                }));

                _context.append($('<a href="#"><i class="bi bi-arrow-clockwise"></i>rotate right</a>').on('click', function(e) {
                  e.stopPropagation();
                  e.preventDefault();
                  _context.close();

                  $('#<?= $_form ?>').trigger('rotate-image-right')

                }));

              }

              _context.append($('<a href="#">remove</a>').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                _context.close();

                $('#<?= $_form ?>').trigger('delete-image')

              }));

              _context.addClose().open(e);

            })
            .on('refresh', function(e) {
              let _me = $(this);
              _me
                .attr('src',
                  _.url('<?= $this->route ?>/imageof/<?= $dto->id ?>?t=' + _.dayjs().unix())

                );

              _.post({
                url: _.url('<?= $this->route ?>'),
                data: {
                  action: 'get-image-mime-type',
                  id: <?= $dto->id ?>
                },

              }).then(d => {
                if ('ack' == d.response) {
                  _me.data('ispdf', 'application/pdf' == d.data ? 'yes' : 'no')

                } else {
                  _.growl(d);

                }

              });

            });

          (c => {

            c.appendTo('#<?= $_uidFileUpload ?>');

            _.fileDragDropHandler.call(c, {
              url: _.url('<?= $this->route ?>'),
              queue: false,
              multiple: false,
              postData: {
                action: 'upload',
                id: <?= $dto->id ?>
              },
              onUpload: d => {
                if ('ack' == d.response) {
                  $('#<?= $_img ?>').trigger('refresh');

                }

              }

            });

          })(_.fileDragDropContainer({
            fileControl: true,
            accept: 'image/jpeg,image/png,application/pdf'

          }));

          $('select, input, textarea', '#<?= $_form ?>')
            .on('change', e => $('#<?= $_btnIssue ?>').prop('disabled', true))

          $('#<?= $_btnIssue ?>')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();

              $('#<?= $_modal ?>').trigger('issue');
              $('#<?= $_modal ?>').modal('hide');

            });

        <?php }  ?>

      }))(_brayworth_);
  </script>

</form>