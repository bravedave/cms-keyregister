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

use currentUser;
use strings;  ?>
<h1 class="d-none d-print-block"><?= $this->title ?></h1>
<div class="form-row mb-2 d-print-none fade" id="<?= $srch = strings::rand() ?>-container">
  <div class="col">
    <input type="search" class="form-control" autofocus id="<?= $srch ?>">

  </div>

  <div class="col-auto d-none d-md-block pt-2">
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="<?= $_uid = strings::rand() ?>" <?= 'yes' == currentUser::option('keyset-include-archived') ? 'checked' : '' ?>>

      <label class="form-check-label" for="<?= $_uid ?>">
        archived

      </label>

    </div>
    <script>
      (_ => {
        $('#<?= $_uid ?>')
          .on('change', function(e) {
            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'set-include-archived',
                value: this.checked ? 'yes' : 'no'
              },

            }).then(d => {
              _.growl(d);
              if ('ack' == d.response) {
                hourglass.on();
                window.location.reload();

              }

            });


          });
      })(_brayworth_);
    </script>

  </div>

  <div class="col-auto d-none d-md-block pt-2">
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="<?= $ooo = strings::rand() ?>">

      <label class="form-check-label" for="<?= $ooo ?>">
        Out Of Office

      </label>

    </div>

  </div>

</div>
<style>
  .text-decoration-line-through {
    text-decoration: line-through;
  }
</style>

<div class="table-responsive">
  <table class="table table-sm fade" id="<?= $tblID = strings::rand() ?>">
    <thead class="small">
      <tr>
        <td class="text-center text-muted border-right" style="width: 50px;" line-number></td>
        <td>keyset</td>
        <td class="text-center" id="<?= $_filterType = strings::rand() ?>">type</td>
        <td>address</td>
        <td class="d-none d-md-table-cell text-center" id="<?= $_filterForRent = strings::rand() ?>">R</td>
        <td class="d-none d-sm-table-cell">name</td>
        <td class="d-none d-md-table-cell">last issue</td>
        <td class="d-none d-md-table-cell">location</td>
        <td class="d-none d-md-table-cell"><i class="bi bi-telephone"></i></td>
        <td class="d-none d-md-table-cell text-center" id="<?= $_filterPM = strings::rand() ?>">PM</td>

      </tr>

    </thead>

    <tbody>
      <?php while ($dto = $this->data->res->dto()) {
        printf(
          '<tr
            class="%s"
            data-id="%s"
            data-properties_id="%s"
            data-address_street="%s"
            data-people_id="%s"
            data-name="%s"
            data-mobile="%s"
            data-keyset_type="%s"
            data-pm="%s"
            data-archived="%s"
            data-forrent="%s">',
          (int)$dto->archived ? 'text-muted' : '',
          $dto->id,
          $dto->properties_id,
          htmlentities($dto->address_street),
          $dto->people_id,
          htmlentities($dto->name),
          $dto->mobile,
          $dto->keyset_type,
          strings::initials($dto->pm),
          (int)$dto->archived,
          $dto->forrent
        ); ?>
        <td class="small text-center text-muted border-right" line-number></td>
        <td><?= $dto->keyset ?></td>
        <td class="text-center"><?= config::keyset_abbreviation($dto->keyset_type) ?></td>
        <td>
          <?= $dto->address_street ?>
          <div class="d-md-none"><?= $dto->name ?: $dto->location ?></div>

        </td>
        <td class="d-none d-md-table-cell text-center"><?= $dto->forrent ? '&check;' : '' ?></td>
        <td class="d-none d-sm-table-cell" data-role="name"><?= $dto->name ?></td>
        <td class="d-none d-md-table-cell" data-role="last-issue"><?php if ($dto->people_id) print strings::asShortDate($dto->maxdate, $time = true) ?></td>
        <td class="d-none d-md-table-cell"><?= $dto->location ?></td>
        <td class="d-none d-md-table-cell" data-role="phone-indicator"><?= strings::isMobilePhone((string)$dto->mobile) ? '<i class="bi bi-telephone" title="has mobile phone"></i>' : '' ?></td>
        <td class="d-none d-md-table-cell text-center"><?= strings::initials($dto->pm) ?></td>

      <?php
        print '</tr>';
      } ?>

    </tbody>

  </table>

</div>
<script>
  (_ => {
    $('#<?= $tblID ?>')
      .on('update-line-numbers', function(e) {
        let tot = 0;
        $('> tbody > tr:not(.d-none) >td[line-number]', this).each((i, e) => {
          $(e).data('line', i + 1).html(i + 1);
          tot++;

        });

        $('> thead > tr >td[line-number]', this).html(tot);

      });

    let pms = [];
    $('#<?= $tblID ?> > tbody > tr')
      .each((i, tr) => {
        let _tr = $(tr);
        let _data = _tr.data();

        if (String(_data.pm) != '') {
          if (pms.indexOf(_data.pm) < 0) {
            pms.push(_data.pm);

          }

        }

        _tr
          .on('archive', function(e) {
            let _me = $(this);
            let _data = _me.data();

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'key-archive',
                id: _data.id

              },

            }).then(d => {
              _.growl(d);
              if ('ack' == d.response) {
                _me
                  .data('archived', 1)
                  .addClass('text-muted');

              }

            });

          })
          .on('archive-undo', function(e) {
            let _me = $(this);
            let _data = _me.data();

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'key-archive-undo',
                id: _data.id

              },

            }).then(d => {
              _.growl(d);
              if ('ack' == d.response) {
                _me
                  .data('archived', 0)
                  .removeClass('text-muted');

              }

            });

          })
          .on('delete', function(e) {
            let _me = $(this);

            _.ask.alert({
              title: 'confirm delete',
              text: 'Are you sure ?',
              buttons: {
                yes: function(e) {
                  $(this).modal('hide');
                  _me.trigger('delete-confirmed');

                }

              }

            });

          })
          .on('delete-confirmed', function(e) {
            let _me = $(this);
            let _data = _me.data();

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'key-delete',
                id: _data.id

              },

            }).then(d => {
              if ('ack' == d.response) {
                _me.remove();

              } else {
                _.growl(d);

              }

            });

          })
          .on('duplicate', function(e) {
            let _me = $(this);
            let _data = _me.data();

            _.get.modal(_.url('<?= $this->route ?>/edit/?t=' + _data.id))
              .then(m => m.on('success', (e, d) => {
                // console.log(e,d);
                _.nav('<?= $this->route ?>?idx=' + d.id);

              }))
              .then(m => m.on('hidden.bs.modal', d => {
                _me[0].scrollIntoView({
                  behavior: "smooth",
                  block: "center"
                }); // Object parameter

                _me.addClass('bg-info');
                setTimeout(() => _me.removeClass('bg-info'), 1000);

              }));

          })
          .on('edit', function(e) {
            let _me = $(this);
            _me.addClass('bg-info');
            let _data = _me.data();

            _.get.modal(_.url('<?= $this->route ?>/edit/' + _data.id))
              .then(m => m.on('success', (e, d) => {
                // console.log(e,d);
                _.nav('<?= $this->route ?>?idx=' + d.id);

              }))
              .then(m => m.on('issue', d => _me.trigger('issue')))
              .then(m => m.on('sms', d => _me.trigger('sms')))
              .then(m => m.on('hidden.bs.modal', d => {
                _me[0].scrollIntoView({
                  behavior: "smooth",
                  block: "center"
                }); // Object parameter

                setTimeout(() => _me.removeClass('bg-info'), 1000);

              }));

          })
          .on('issue', function(e) {
            let _me = $(this);
            _me.addClass('bg-info');
            let _data = _me.data();

            _.get.modal(_.url('<?= $this->route ?>/issue/' + _data.id))
              .then(m => m.on('success', (e, d) => {
                // console.log(e,d);
                _.nav('<?= $this->route ?>?idx=' + d.id);

              }))
              .then(m => m.on('hidden.bs.modal', d => {
                _me[0].scrollIntoView({
                  behavior: "smooth",
                  block: "center"
                }); // Object parameter

                setTimeout(() => _me.removeClass('bg-info'), 1000);

              }));

          })
          .on('issue-return', function(e) {
            let _me = $(this);
            _me.addClass('bg-info');
            let _data = _me.data();

            _.post({
              url: _.url('<?= $this->route ?>'),
              data: {
                action: 'key-return',
                id: _data.id
              },

            }).then(d => {
              if ('ack' == d.response) {
                $('td[data-role="name"], td[data-role="last-issue"], td[data-role="phone-indicator"]', _me).html('');
                _me.removeData('mobile');
                _me.removeData('people_id');
              } else {
                _.growl(d);

              }

              setTimeout(() => _me.removeClass('bg-info'), 1000);

            });

          })
          .on(_.browser.isMobileDevice ? 'click' : 'contextmenu', function(e) {
            if (e.shiftKey)
              return;

            e.stopPropagation();
            e.preventDefault();
            _.hideContexts();

            let _tr = $(this);
            let _data = _tr.data();
            let _context = _.context();

            _context.append($('<a href="#"><i class="bi bi-person-plus"></i><strong>issue</strong></a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              _tr.trigger('issue');

            }));

            if (Number(_data.people_id) > 0) {
              _context.append($('<a href="#"><i class="bi bi-person-x"></i>return</a>').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                _context.close();

                _tr.trigger('issue-return');

              }));

            }

            _context.append($('<a href="#">view log</a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              _tr.trigger('view-log');

            }));

            _context.append($('<a href="#"><i class="bi bi-pencil"></i>edit</a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              _tr.trigger('edit');

            }));

            _context.append($('<a href="#"><i class="bi bi-file-plus"></i>edit as new</a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              _tr.trigger('duplicate');

            }));

            _context.append($('<a href="#"><i class="bi bi-chat-dots"></i>send sms</a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              _tr.trigger('sms');

            }));

            if (Number(_data.people_id) > 0) {
              _context.append(
                $('<a target="_blank"></a>')
                .html('goto ' + _data.name)
                .prepend('<i class="bi bi-box-arrow-up-right"></i>')
                .on('click', e => _context.close())
                .attr('href', _.url('person/view/' + _data.people_id))

              );

            }

            if (Number(_data.properties_id) > 0) {
              _context.append(
                $('<a target="_blank"></a>')
                .html('goto ' + _data.address_street)
                .prepend('<i class="bi bi-box-arrow-up-right"></i>')
                .on('click', e => _context.close())
                .attr('href', _.url('property/view/' + _data.properties_id))

              );

            }

            // _context.append($('<a href="#"><i class="bi bi-trash"></i>delete</a>').on('click', function(e) {
            //   e.stopPropagation();
            //   e.preventDefault();
            //   _context.close();

            //   _tr.trigger('delete');

            // }));

            _context.append(
              $('<a href="#">archive</a>')
              .on('recon', function(e) {
                if (1 == Number(_data.archived)) {
                  $(this)
                    .on('click', function(e) {
                      e.stopPropagation();
                      e.preventDefault();
                      _context.close();

                      _tr.trigger('archive-undo');

                    })
                    .prepend('<i class="bi bi-archive-fill"></i>')

                } else {
                  $(this)
                    .on('click', function(e) {
                      e.stopPropagation();
                      e.preventDefault();
                      _context.close();

                      _tr.trigger('archive');

                    })
                    .prepend('<i class="bi bi-archive"></i>')

                }
              })
              .trigger('recon')
            );

            _context.addClose().open(e);

          })
          .on('sms', function(e) {
            let _tr = $(this);
            let _data = _tr.data();

            if (String(_data.mobile).IsMobilePhone()) {
              if (!!window._cms_) {
                _cms_.modal.sms({
                  to: _data.mobile
                });

              } else {
                _.ask.warning({
                  title: 'Warning',
                  text: 'no SMS program'
                });

              }

            } else {
              _.ask.warning({
                title: 'Warning',
                text: 'no mobile number'
              });

            }

          })
          .on('view-log', function(e) {
            let _me = $(this);
            _me.addClass('bg-info');
            let _data = _me.data();

            _.get.modal(_.url('<?= $this->route ?>/viewlog/' + _data.id))
              .then(m => m.on('hidden.bs.modal', d => {
                _me[0].scrollIntoView({
                  behavior: "smooth",
                  block: "center"
                }); // Object parameter

                setTimeout(() => _me.removeClass('bg-info'), 1000);

              }));

          });

        if (!_.browser.isMobileDevice) {
          _tr
            .addClass('pointer')
            .on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _.hideContexts();

              $(this).trigger('issue');

            })

        }

      });

    let filterPM = '';
    if (pms.length > 0) {

      $('#<?= $_filterPM ?>')
        .on('contextmenu', function(e) {
          if (e.shiftKey)
            return;

          e.stopPropagation();
          e.preventDefault();

          _.hideContexts();

          let _context = _.context();

          $.each(pms, (i, pm) => {
            _context.append(
              $('<a href="#"></a>')
              .html(pm)
              .on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                _context.close();

                filterPM = $(this).html();
                $('#<?= $_filterPM ?>').html('').append($('<div class="badge badge-primary"></div>').html(filterPM));
                $('#<?= $srch ?>').trigger('search');

              })
              .on('reconcile', function() {
                if (pm == filterPM) $(this).prepend('<i class="bi bi-check"></i>')

              })
              .trigger('reconcile')

            );

          });

          _context.append('<hr>');
          _context.append(
            $('<a href="#">clear</a>').on('click', function(e) {
              e.stopPropagation();
              e.preventDefault();
              _context.close();

              filterPM = '';
              $('#<?= $_filterPM ?>').html('PM');
              $('#<?= $srch ?>').trigger('search');

            })
          );

          _context.open(e);

        });

    }

    $(document)
      .ready(() => {
        <?php if ($this->data->idx) {  ?>
          let tr = $('#<?= $tblID ?> > tbody > tr[data-id="<?= $this->data->idx ?>"]');
          if (tr.length > 0) {
            tr[0].scrollIntoView({
              block: "center"
            });

            tr.addClass('bg-light');
            setTimeout(() => tr.removeClass('bg-light'), 3000);

          }

        <?php } elseif ($this->data->rex) {  ?>
          let tr = $('#<?= $tblID ?> > tbody > tr[data-id="<?= $this->data->rex ?>"]');
          if (tr.length > 0) {
            tr[0].scrollIntoView({
              behavior: "smooth",
              block: "center"
            }); // Object parameter

            tr.trigger('edit');

          }

        <?php }  ?>

        $('#<?= $tblID ?>').addClass('show');

      });

    let filterType = '';
    $('#<?= $_filterType ?>')
      .on('contextmenu', function(e) {
        if (e.shiftKey)
          return;

        e.stopPropagation();
        e.preventDefault();

        _.hideContexts();

        let _context = _.context();

        _context.append(
          $('<a href="#"><?= config::keyset_management_label ?></a>')
          .on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterType = '<?= config::keyset_management ?>';
            localStorage.setItem('keyregister-filter-type', filterType);
            $('#<?= $srch ?>').trigger('search');

          })
          .on('reconcile', function() {
            if ('<?= config::keyset_management ?>' == filterType) $(this).prepend('<i class="bi bi-check"></i>')

          })
          .trigger('reconcile')

        );

        _context.append(
          $('<a href="#"><?= config::keyset_tenant_label ?></a>')
          .on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterType = '<?= config::keyset_tenant ?>';
            localStorage.setItem('keyregister-filter-type', filterType);
            $('#<?= $srch ?>').trigger('search');

          })
          .on('reconcile', function() {
            if ('<?= config::keyset_tenant ?>' == filterType) $(this).prepend('<i class="bi bi-check"></i>')

          })
          .trigger('reconcile')

        );

        _context.append(
          $('<a href="#">clear</a>').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterType = '';
            localStorage.removeItem('keyregister-filter-type');
            $('#<?= $srch ?>').trigger('search');

          })
        );

        _context.open(e);

      });

    let filterForRent = 0;
    $('#<?= $_filterForRent ?>')
      .on('contextmenu', function(e) {
        if (e.shiftKey)
          return;

        e.stopPropagation();
        e.preventDefault();

        _.hideContexts();

        let _context = _.context();

        _context.append(
          $('<a href="#">Show for Rent</a>')
          .on('click', e => {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterForRent = 1;
            localStorage.setItem('keyregister-filter-forrent', 1);
            $(this).trigger('set-indicator');
            $('#<?= $srch ?>').trigger('search');

          })
          .on('reconcile', function() {
            if (1 == filterForRent) $(this).prepend('<i class="bi bi-check"></i>')

          })
          .trigger('reconcile')

        );

        _context.append(
          $('<a href="#">Show NOT for Rent</a>')
          .on('click', e => {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterForRent = 2;
            localStorage.setItem('keyregister-filter-forrent', 2);
            $(this).trigger('set-indicator');
            $('#<?= $srch ?>').trigger('search');

          })
          .on('reconcile', function() {
            if (2 == filterForRent) $(this).prepend('<i class="bi bi-check"></i>')

          })
          .trigger('reconcile')

        );

        _context.append(
          $('<a href="#">Show All</a>')
          .on('click', e => {
            e.stopPropagation();
            e.preventDefault();
            _context.close();

            filterForRent = 0;
            localStorage.removeItem('keyregister-filter-forrent');
            $(this).trigger('set-indicator');
            $('#<?= $srch ?>').trigger('search');

          })
          .on('reconcile', function() {
            if (0 == filterForRent) $(this).prepend('<i class="bi bi-check"></i>')

          })
          .trigger('reconcile')

        );

        _context.open(e);

      })
      .on('set-indicator', function(e) {
        if (1 == filterForRent) {
          $(this)
            .removeClass('text-decoration-line-through')
            .addClass('bg-secondary text-white');

        } else if (2 == filterForRent) {
          $(this)
            .removeClass('bg-secondary text-white')
            .addClass('text-decoration-line-through');

        } else {
          $(this)
            .removeClass('bg-secondary text-white text-decoration-line-through');

        }

      });

    $('#<?= $ooo ?>')
      .on('change', e => $('#<?= $srch ?>').trigger('search'))

    let srchidx = 0;
    $('#<?= $srch ?>')
      .on('search', function(e) {
        let idx = ++srchidx;
        let txt = this.value;

        let _tbl = $('#<?= $tblID ?>');
        let _tbl_data = _tbl.data();

        let oof = $('#<?= $ooo ?>').prop('checked');
        // console.log(oof);

        $('#<?= $tblID ?> > tbody > tr').each((i, tr) => {
          if (idx != srchidx) return false;

          let _tr = $(tr);
          let _data = _tr.data();

          if (oof && Number(_data.people_id) < 1) {
            _tr.addClass('d-none');

          } else if (1 == filterForRent && _data.forrent != 1) {
            _tr.addClass('d-none');

          } else if (2 == filterForRent && _data.forrent == 1) {
            _tr.addClass('d-none');

          } else if (filterType != '' && _data.keyset_type != filterType) {
            _tr.addClass('d-none');

          } else if (filterPM != '' && _data.pm != filterPM) {
            _tr.addClass('d-none');

          } else if ('' == txt.trim()) {
            _tr.removeClass('d-none');

          } else {
            let str = _tr.text()
            if (str.match(new RegExp(txt, 'gi'))) {
              _tr.removeClass('d-none');

            } else {
              _tr.addClass('d-none');

            }

          }

        });

        $('#<?= $tblID ?>').trigger('update-line-numbers');

      })
      .on('keyup', function(e) {
        $(this).trigger('search')
      });

    if (!!localStorage.getItem('keyregister-filter-type') || !!localStorage.getItem('keyregister-filter-forrent')) {
      if (!!localStorage.getItem('keyregister-filter-type')) {
        filterType = localStorage.getItem('keyregister-filter-type');
      }
      if (!!localStorage.getItem('keyregister-filter-forrent')) {
        filterForRent = localStorage.getItem('keyregister-filter-forrent');
        $('#<?= $_filterForRent ?>').trigger('set-indicator');
      }
      $('#<?= $srch ?>').trigger('search');
    } else {
      $('#<?= $tblID ?>').trigger('update-line-numbers');

    }

    $('#<?= $srch ?>-container')
      .addClass('show');

    $(document)
      .on('key-search', (e, v) => {
        if (Number(v) > 0) {
          let tr = $('#<?= $tblID ?> > tbody > tr[data-id="' + v + '"]');
          if (tr.length > 0) {
            tr[0].scrollIntoView({
              behavior: "smooth",
              block: "center"
            }); // Object parameter

            tr.trigger('edit');

          }

        }

      });

  })(_brayworth_);
</script>