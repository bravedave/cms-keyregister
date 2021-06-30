<?php

/**
 * David Bray
 * BrayWorth Pty Ltd
 * e. david@brayworth.com.au
 *
 * MIT License
 *
 */

class launcher {
	static function run() {
		if (class_exists('dvc\application')) {
			/**
			 * Extended example, uses an application directory structure
			 *
			 * To use this example, install bravedave/dvc
			 * 	composer require bravedave/dvc
			 *
			 * then review the folders
			 *  controller
			 *  app
			 */
			application::run();
		} elseif (class_exists('Slim\Factory\AppFactory')) {
			$app = Slim\Factory\AppFactory::create();

			$app->get('/', function (
				Psr\Http\Message\ServerRequestInterface $request,
				Psr\Http\Message\ResponseInterface $response,
				$args
			) {

				$response->getBody()->write("Hello world!");
				return $response;
			});

			$app->get('/hello/{name}', function ($request, $response, $args) {
				$renderer = new Slim\Views\PhpRenderer(__DIR__ . '/slim', [
					'title' => 'Hello World',

				]);

				return $renderer->render($response, "layout.phtml", [
					'name' => $args['name']

				]);
			})->setName('profile');

			$app->run();
		} elseif (class_exists('pages\page')) {
			/**
			 * use the tutorial from https://github.com/bravedave/pages
			 * to activate this extension
			 *
			 */
			$page = new pages\page;  // from outside this namespace
			$page->open();

			print Parsedown::instance()->text(file_get_contents(__DIR__ . '/../../Readme.md'));
		} elseif (class_exists('Parsedown')) {
			/**
			 * Well not that simple - you have extended it with
			 * composer require erusev/parsedown
			 */

			print '<html><body>';
			print Parsedown::instance()->text(file_get_contents(__DIR__ . '/../../Readme.md'));
			print '</body></html>';
		} else {

			/**
			 * Yeah - the Minimum Viable Product
			 */
			header('Content-Type: text/plain');
			print 'see https://github.com/bravedave/mvp';
		}
	}
}
