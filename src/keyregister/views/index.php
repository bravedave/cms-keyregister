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

<ul class="nav flex-column">
  <li class="nav-item h6"><a href="<?= strings::url($this->route) ?>">Index</a></li>
  <?php if (!$this->data->count) { ?>
    <li class="nav-item my-4"><a href="#" class="nav-link" id="<?= $_import = strings::rand() ?>">Import Default Set</a></li>
    <script>
      (_ => {
        $('#<?= $_import ?>').on('click', function(e) {
          e.stopPropagation();
          e.preventDefault();

          _.post({
            url: _.url('<?= $this->route ?>'),
            data: {
              action: 'import-default-dataset'

            },

          }).then(d => {
            if ('ack' == d.response) {
              _.nav('<?= $this->route ?>');

            } else {
              _.growl(d);

            }

          });

        })

      })(_brayworth_);
    </script>

  <?php } ?>

  <li class="nav-item"><a href="#" class="nav-link" id="<?= $_uidFreeSet = strings::rand() ?>"><?= config::label_freeset ?></a></li>
  <li class="nav-item my-4"><button class="btn btn-block btn-outline-secondary" id="<?= $_btnAdd = strings::rand() ?>"><i class="bi bi-plus"></i> new</button></li>
  <script>
    (_ => {
      $('#<?= $_uidFreeSet ?>').on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();

        _.get.modal(_.url('<?= $this->route ?>/freeset'));

      });

      $('#<?= $_btnAdd ?>').on('click', function(e) {
        e.stopPropagation();
        e.preventDefault();

        _.get.modal(_.url('<?= $this->route ?>/edit'))
          .then(m => m.on('success', d => _.nav('<?= $this->route ?>')));

      });

    })(_brayworth_);
  </script>

  <?php if (currentUser::option('google-sharer')) { ?>
    <li class="nav-item h6 pt-3 pl-3">
      Reference Documents

    </li>

    <li class="nav-item">
      <a class="nav-link pl-4" href="https://docs.google.com/document/d/1zCZmBMsc6kV46YWjs6NyMRwnFa2k-XCnzOLy6nBAtvU/" target="_blank">
        <i class="bi bi-file-richtext text-primary"></i> Key Register

      </a>

    </li>

  <?php }  ?>
  <li class="nav-item">
    <a class="nav-link pl-4" href="<?= strings::url($this->route . '/doc/key-register') ?>" target="_blank">
      <i class="bi bi-file-pdf text-danger"></i> Key Register

    </a>

  </li>

</ul>

<div class="form-row mt-4 d-none">
  <div class="col">
    <div class="input-group">
      <input type="number" class="form-control" id="<?= $_uid = strings::rand() ?>" placeholder="uid">

      <div class="input-group-append">
        <button type="button" class="btn input-group-text" id="<?= $_uid ?>btn"><i class="bi bi-arrow-return-left"></i></button>

      </div>

    </div>
    <script>
      (_ => {
        $('#<?= $_uid ?>')
          .on('search', function(e) {
            let _me = $(this);
            let v = _me.val();

            if (Number(v) > 0) $(document).trigger('key-search', v);

          })
          .on('keyup', function(e) {

            if (13 == e.keyCode) {
              e.stopPropagation();
              $(this).trigger('search');

            }

          });

        $('<?= $_uid ?>btn').on('click', function(e) {
          e.stopPropagation();
          e.preventDefault();

          $('#<?= $_uid ?>').trigger('search');

        });

      })(_brayworth_);
    </script>

  </div>

</div>

<div class="form-row mt-4 d-none d-md-flex">
  <div class="col">
    <a class="nav-link" href="<?= strings::url(sprintf('%s/imagekeycheckout/v', $this->route)) ?>" id="<?= $_uid = strings::rand() ?>">
      <img class="img-fluid" src="<?= strings::url(sprintf('%s/imagekeycheckout', $this->route)) ?>">

    </a>
    <script>
      (_ => $(document).ready(() => {
        $('#<?= $_uid ?>').on('contextmenu', function(e) {
          if (e.shiftKey)
            return;

          e.stopPropagation();
          e.preventDefault();

          _.hideContexts();

          let _me = $(this);
          let _context = _.context();

          _context.append(
            $('<a>view QR Code</a>')
            .attr('href', _me.attr('href'))
            .on('click', e => _context.close())

          );

          _context.append(
            $('<a href="<?= strings::url(config::$KEYCHECKOUT) ?>">key checkout</a>')
            .on('click', e => _context.close())

          );

          _context.addClose().open(e);
        });
      }))(_brayworth_);
    </script>

  </div>

</div>
