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

<style media="print">
  footer {
    display: none;
  }
</style>

<div class="row mt-4">
  <div class="col text-center">
    <img class="img-fluid" src="<?= strings::url(sprintf('%s/imagekeycheckout', $this->route)) ?>">

  </div>

</div>