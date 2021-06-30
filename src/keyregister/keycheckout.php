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

use Json;
use strings;

class keycheckout extends \Controller {
  public $RequireValidation = false;
  protected $viewPath = __DIR__ . '/views/';

  protected function _index() {
    $this->render([
      'content' => 'checkout',

    ]);
  }

  protected function access_control() {
    return (true);  // don't access control the home page !

  }

  protected function posthandler() {
    $action = $this->getPost('action');

    if ('get-person-by-mobile' == $action) {
      $mobile = (string)$this->getPost('mobile');
      if (strings::isMobilePhone($mobile)) {
        $dao = new \dao\people;
        if ($dto = $dao->getByPHONE($mobile)) {
          Json::ack($action)
            ->add('data', [
              'id' => $dto->id,
              'name' => $dto->name
            ]);
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('get-keys-for-person' == $action) {
      /*
        (_ => {
          _.post({
            url : _.url('keycheckout'),
            data : {
              action : 'get-keys-for-person',
              id : '0418745334'

            },

          }).then( d => console.log(d));

        })(_brayworth_);
      */
      $mobile = (string)$this->getPost('mobile');
      if (strings::isMobilePhone($mobile)) {
        $dao = new \dao\people;
        if ($dto = $dao->getByPHONE($mobile)) {
          $dao = new dao\keyregister;
          Json::ack($action)
            ->add('data', $dao->getKeysForPerson($dto->id));
        } else {
          Json::nak($action .  ' -  not found');
        }
      } else {
        Json::nak($action .  ' -  missing mobile');
      }
    } elseif ('key-checkout' == $action) {
      if ($id = (int)$this->getPost('id')) {

        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {

          $direction = $this->getPost('direction') ?: 'issue';
          $a = [
            'people_id' => 'issue' == $direction ? (int)$this->getPost('people_id') : 0,
            'updated' => \db::dbTimeStamp()

          ];

          $dao->UpdateByID($a, $id);
          $a = [
            'keyregister_id' => $id,
            'people_id' => (int)$this->getPost('people_id'),
            'description' => $direction,
            'date' => \db::dbTimeStamp()

          ];

          $dao = new dao\keyregister_log;
          $dao->Insert($a);

          $dao = new dao\keyregister;
          Json::ack($direction . ' keyset')
            ->add('data', [
              'keys' => $dao->getKeysForPerson((int)$this->getPost('people_id'))
            ]);
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } else {
      parent::postHandler();
    }
  }

  protected function render($params) {

    $defaults = [
      'primary' => false,
      'secondary' => false,
      'content' => false,
      'fullcalendar' => false,
      'googlestreetview' => false,
      'googlemapsapi' => false,
      'navbar' => 'navbar-empty',
      'footer' => false,
      'template' => '\dvc\pages\bootstrap4'

    ];

    $options = array_merge($defaults, $params);

    return (parent::render($options));
  }

  public function checkout() {
    if ($k = $this->getParam('k')) {
      $dao = new dao\keyregister;
      if ($dto = $dao->getByKeySet($k)) {

        $dto = $dao->getRichData($dto);

        $this->data = (object)[
          'title' => $this->title = sprintf('%s #%d', config::label_issue, $dto->keyset),
          'dto' => $dto

        ];

        $this->load('checkout-issue');
      } else {
        $this->load('checkout-not-found');
      }
    } else {
      $this->load('checkout-not-found');
    }
  }
}
