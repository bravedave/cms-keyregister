<?php
/*
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
*/

namespace cms\keyregister;  ?>

<style media="print">
  footer { display: none; }
</style>

<div class="row mt-4">
  <div class="col text-center">
    <img class="img-fluid" src="<?= $this->data->qrpath ?>">

  </div>

</div>

<div class="row">
  <div class="col text-center text-truncate" style="font-size: 72pt;">uid : <?= $this->data->dto->id ?></div>

</div>
