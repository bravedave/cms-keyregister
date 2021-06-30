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
use theme;  ?>

<form id="<?= $_form = strings::rand() ?>" autocomplete="off">
  <div class="modal fade" tabindex="-1" role="dialog" id="<?= $_modal = strings::rand() ?>" aria-labelledby="<?= $_modal ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header <?= theme::modalHeader() ?>">
          <h5 class="modal-title" id="<?= $_modal ?>Label"><?= $this->title ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <table class="table table-sm">
            <thead class="small">
              <tr>
                <td>date</td>
                <td>name</td>
                <td>description</td>

              </tr>

            </thead>
            <tbody>
              <?php while ($dto = $this->data->res->dto()) { ?>
                <tr>
                  <td><?= strings::asShortDate($dto->date, $time  = true) ?></td>
                  <td><?= $dto->name ?></td>
                  <td><?= $dto->description ?></td>

                </tr>

              <?php } ?>
            </tbody>

          </table>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">close</button>

        </div>

      </div>

    </div>

  </div>

</form>