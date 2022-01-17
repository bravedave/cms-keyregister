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

use strings, theme; ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php
          $no = 0;
          $nos = [];
          foreach ($this->data->freeset as $v) {
            if ((int)$v > $no) {
              if ($nos) printf('<div class="row"><div class="col">%s</div></div>', implode(', ', $nos));
              $no = ((int)((int)$v / 100)) * 100;
              printf('<div class="row"><div class="col"><strong>%s</strong></div></div>', $no);
              $no += 100;
              $nos = [];
            }
            $nos[] = $v;
          }
          if ($nos) printf('<div class="row"><div class="col">%s</div></div>', implode(', ', $nos));
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>
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

          return false;
        });
    }))(_brayworth_);
  </script>
</form>