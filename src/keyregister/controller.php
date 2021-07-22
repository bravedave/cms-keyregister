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

// use currentUser;
use FilesystemIterator;
use Json;
use strings;
use sys;
use BaconQrCode as QrCode;
use currentUser;

class controller extends \Controller {
  protected $viewPath = __DIR__ . '/views/';

  protected function _index() {

    $dao = new dao\keyregister;
    $this->data = (object)[
      'title' => $this->title = config::label,
      'res' => $dao->getDataSet(),
      'count' => $dao->getRecordCount(),
      'idx' => $this->getParam('idx'),
      'rex' => $this->getParam('rex'),

    ];

    $this->render([
      'title' => $this->title,
      'primary' => 'matrix',
      'secondary' => 'index',
      'data' => (object)[
        'searchFocus' => false,
        'pageUrl' => strings::url($this->route)

      ],

    ]);
  }

  protected function before() {
    config::keyregister_checkdatabase();
    parent::before();
  }

  protected function posthandler() {
    $action = $this->getPost('action');

    // sys::logger(sprintf('<%s> %s', $action, __METHOD__));

    if ('key-delete' == $action) {
      $id = (int)$this->getPost('id');
      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($id)) {

        if ($path = $dao->getStore($dto)) {
          $gi = new FilesystemIterator($path, FilesystemIterator::KEY_AS_FILENAME);
          foreach ($gi as $key => $item) {
            unlink($item->getRealPath());
          }

          rmdir($path);
        }

        $dao->delete($dto->id);
        Json::ack($action);
      } else {
        Json::nak($action);
      }
    } elseif ('key-remove-image' == $action) {
      if ($id = (int)$this->getPost('id')) {
        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {
          if ($path = $dao->getImagePath($dto)) {
            unlink($path);
            Json::ack($action);
          } else {
            Json::nak($action);
          }
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('key-issue' == $action) {
      if ($id = (int)$this->getPost('id')) {

        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {

          $a = [
            'people_id' => (int)$this->getPost('people_id'),
            'updated' => \db::dbTimeStamp()

          ];

          $dao->UpdateByID($a, $id);
          $a = [
            'keyregister_id' => $id,
            'people_id' => (int)$this->getPost('people_id'),
            'description' => 'issue',
            'date' => \db::dbTimeStamp()

          ];

          $dao = new dao\keyregister_log;
          $dao->Insert($a);

          Json::ack($action)
            ->add('id', $id);
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('key-return' == $action) {
      if ($id = (int)$this->getPost('id')) {

        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {

          $a = [
            'people_id' => 0,
            'updated' => \db::dbTimeStamp()

          ];

          $dao->UpdateByID($a, $id);
          $a = [
            'keyregister_id' => $id,
            'people_id' => (int)$dto->people_id,
            'description' => 'return',
            'date' => \db::dbTimeStamp()

          ];

          $dao = new dao\keyregister_log;
          $dao->Insert($a);

          Json::ack($action)
            ->add('id', $id);
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('key-save' == $action) {
      $a = [
        'keyset' => $this->getPost('keyset'),
        'keyset_type' => (int)$this->getPost('keyset_type'),
        'location' => $this->getPost('location'),
        'properties_id' => (int)$this->getPost('properties_id'),
        'description' => $this->getPost('description'),
        'updated' => \db::dbTimeStamp()

      ];

      $id = (int)$this->getPost('id');
      $dao = new dao\keyregister;
      if ($id) {
        $dao->UpdateByID($a, $id);
      } else {
        $a['created'] = $a['updated'];

        $a['keyset_type'] = config::keyset_management;
        $id = $dao->Insert($a);

        $a['keyset_type'] = config::keyset_tenant;
        $dao->Insert($a);
      }

      Json::ack($action)
        ->add('id', $id); // return id of management set
    } elseif ('get-keys-for-person' == $action) {
      /*
        (_ => {
          _.post({
            url : _.url('keyregister'),
            data : {
              action : 'get-keys-for-person',
              id : 345

            },

          }).then( d => console.log(d));

        })(_brayworth_);
      */
      if ($id = (int)$this->getPost('id')) {
        $dao = new dao\keyregister;
        Json::ack($action)
          ->add('data', $dao->getKeysForPerson($id));
      } else {
        Json::nak($action .  ' -  missing id');
      }
    } elseif ('get-keys-for-property' == $action) {
      /*
        (_ => {
          _.post({
            url : _.url('keyregister'),
            data : {
              action : 'get-keys-for-property',
              id : 8473

            },

          }).then( d => console.log(d));

        })(_brayworth_);
      */
      if ($id = (int)$this->getPost('id')) {
        $dao = new dao\keyregister;
        Json::ack($action)
          ->add('data', $dao->getKeysForProperty($id));
      } else {
        Json::nak($action .  ' -  missing id');
      }
    } elseif ('get-image-mime-type' == $action) {
      if ($id = (int)$this->getPost('id')) {
        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {
          Json::ack($action)
            ->add('data', $dao->getImageMimeType($dto));
        } else {
          Json::nak($action . ' - not found');
        }
      } else {
        Json::nak($action .  ' -  missing id');
      }

    } elseif ('rotate-image-right' == $action || 'rotate-image-left' == $action) {
      $id = (int)$this->getPost('id');
      if ($id = (int)$this->getPost('id')) {
        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {
          if ($path = $dao->getImagePath($dto)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
            $strType = finfo_file($finfo, $path);

            $accept = [
              'image/png',
              'image/x-png',
              'image/jpeg',
              'image/pjpeg',

            ];

            if (in_array($strType, $accept)) {

              $ext = [
                'image/png' => '.png',
                'image/x-png' => '.png',
                'image/jpeg' => '.jpg',
                'image/pjpeg' => '.jpg',

              ][$strType];

              // \sys::logger( sprintf( '%s (%s) : %s', $path, $strType, __METHOD__));

              $img = '.png' == $ext ? \imagecreatefrompng($path) : imagecreatefromjpeg($path);

              if ($action == 'rotate-image-right') {
                $img = imagerotate($img, -90, 0);
              } elseif ($action == 'rotate-image-left') {
                $img = imagerotate($img, 90, 0);
              }

              unlink($path);
              if ('.png' == $ext) {
                imagepng($img, $path);
              } else {
                imagejpeg($img, $path);
              }

              Json::ack($action);
            } else {
              Json::nak($action);
            }
          } else {
            Json::nak($action);
          }
        } else {
          Json::nak($action);
        }
      } else {
        Json::nak($action);
      }
    } elseif ('upload' == $action) {
      if ($_FILES) {
        if ($id = $this->getPost('id')) {

          $dao = new dao\keyregister;
          if ($dto = $dao->getByID($id)) {
            if ($store = $dao->getStore($dto)) {
              foreach ($_FILES as $file) {
                $uploader = new fileUploader([
                  'path' => $store,
                  'accept' => [
                    'image/png',
                    'image/x-png',
                    'image/jpeg',
                    'image/pjpeg',
                    'application/pdf'

                  ]

                ]);

                if ($uploader->save($file, $name = 'image', $delete = ['image.png', 'image.jpg', 'image.jpeg', 'image.pdf'])) {
                  Json::ack($action);
                } else {
                  Json::nak($action);
                }

                break;  // only 1 file

              }
            } else {
              Json::nak($action);
            }
          } else {
            Json::nak($action);
          }
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

  public function doc($doc = '') {
    if ('key-register' == $doc) {
      sys::serve(__DIR__ . '/resource/Key Register.pdf');
    }
  }

  public function edit($id = 0) {
    $dto = new dao\dto\keyregister;
    $this->title = config::label_new;

    if ($id = (int)$id) {
      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($id)) {
        $dto = $dao->getRichData($dto);
        $this->title = sprintf('%s #%d', config::label_edit, $dto->id);
      }
    } elseif ($tid = (int)$this->getParam('t')) {
      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($tid)) {
        $dto = $dao->getRichData($dto);
        $dto->id = 0;
      }
    }

    $this->data = (object)[
      'title' => $this->title,
      'dto' => $dto

    ];

    $this->load('edit');
  }

  public function pdfof(int $id = 0) {
    if ($id = (int)$id) {
      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($id)) {
        if ('application/pdf' == $dao->getImageMimeType($dto)) {
          if ($path = $dao->getImagePath($dto)) {
            sys::serve($path);
            return;
          }
        }
      }
    }

    print 'not found';
  }

  public function imageof(int $id = 0) {
    if ($id = (int)$id) {
      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($id)) {
        if ('application/pdf' == $dao->getImageMimeType($dto)) {
          sys::serve(__DIR__ . '/resource/file-pdf.svg');
          return;
        } else {
          if ($path = $dao->getImagePath($dto)) {
            sys::serve($path);
            return;
          }
        }
      }
    }

    sys::serve(__DIR__ . '/resource/blank-16-9.svg');
  }

  public function issue($id = 0) {
    if ($id = (int)$id) {

      $dao = new dao\keyregister;
      if ($dto = $dao->getByID($id)) {

        $dto = $dao->getRichData($dto);

        $this->data = (object)[
          'title' => $this->title = sprintf('%s #%d', config::label_issue, $dto->id),
          'dto' => $dto

        ];

        $this->load('issue');
      } else {
        $this->load('modal-not-found');
      }
    } else {
      $this->load('modal-not-found');
    }
  }

  public function qrcode($id = 0, $v = '') {

    $url = strings::url(
      sprintf(
        '%s/?rex=%d',
        $this->route,
        $id

      ),
      $protocol = true

    );

    if ('v' == $v) {
      if ($id = (int)$id) {
        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {

          $this->data = (object)[
            'qrpath' => strings::url($this->route . '/qrcode/' . $id, $protocol = true),
            'url' => $url,
            'dto' => $dto

          ];

          $this->render(['content' => 'qr-code']);
        } else {
          $this->render(['content' => 'not-found']);
        }
      } else {
        $this->render(['content' => 'not-found']);
      }
    } else {
      if ($id = (int)$id) {
        $dao = new dao\keyregister;
        if ($dto = $dao->getByID($id)) {

          $qrCode = $dao->getQRPath($dto);
          // sys::logger( sprintf('<%s> %s', $qrCode, __METHOD__));

          if (!\file_exists($qrCode)) {
            $renderer = new QrCode\Renderer\ImageRenderer(
              new QrCode\Renderer\RendererStyle\RendererStyle(800, $margin = 0),
              new QrCode\Renderer\Image\SvgImageBackEnd()

            );

            $writer = new QrCode\Writer($renderer);
            $writer->writeFile($url, $qrCode);
          }

          if (file_exists($qrCode)) {
            sys::serve($qrCode);
          } else {
            sys::serve(__DIR__ . '/resource/blank.svg');
          }
        } else {
          sys::serve(__DIR__ . '/resource/blank.svg');
        }
      } else {
        sys::serve(__DIR__ . '/resource/blank.svg');
      }
    }
  }

  public function imagekeycheckout($v = '') {

    if ('v' == $v) {
      $this->render(['content' => 'qr-code-anon']);
    } else {
      $url = strings::url(config::$KEYCHECKOUT, $protocol = true);
      // \sys::logger( sprintf('<%s> %s', md5( strings::getGUID()), __METHOD__));

      $qrCode = sprintf(
        '%s/checkout.svg',
        config::keyregister_Path()

      );

      if (!\file_exists($qrCode)) {
        $renderer = new QrCode\Renderer\ImageRenderer(
          new QrCode\Renderer\RendererStyle\RendererStyle(800, $margin = 0),
          new QrCode\Renderer\Image\SvgImageBackEnd()

        );

        $writer = new QrCode\Writer($renderer);
        $writer->writeFile($url, $qrCode);
      }

      if (file_exists($qrCode)) {
        sys::serve($qrCode);
      } else {
        sys::serve(__DIR__ . '/resource/blank.svg');
      }
    }
  }

  public function viewlog($id) {
    if ($id = (int)$id) {

      $dao = new dao\keyregister_log;
      if ($res = $dao->getForID($id)) {

        $this->data = (object)[
          'title' => $this->title = config::label_issue_log,
          'res' => $res

        ];

        $this->load('issue-log');
      } else {
        $this->load('modal-not-found');
      }
    } else {
      $this->load('modal-not-found');
    }
  }
}
